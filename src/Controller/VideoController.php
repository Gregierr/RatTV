<?php

namespace App\Controller;

use App\Exception\VideoNotFoundException;
use App\Form\CommentType;
use App\Form\SearchType;
use App\Service\CommentService;
use App\Service\VideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use App\Form\VideoType;

class VideoController extends AbstractController
{
    public function __construct(private VideoService   $videoService,
                                private RequestStack   $requestStack,
                                private CommentService $commentService,)
    {
    }

    #[Route('/video/upload', name: 'video_upload')]
    public function uploadVideo(Request $request): Response
    {
        $form = $this->createForm(VideoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('file')->getData();

            $newFilename = uniqid() . '.' . $videoFile->guessExtension();

            $videoFile->move(
                $this->getParameter('video_directory'),
                $newFilename
            );
            $session = $this->requestStack->getSession();

            try {
                $this->videoService->saveVideo(
                    $newFilename,
                    $session->get("id"),
                    $form->getData()
                );
            } catch (AccessDeniedException $e) {
                return $this->json($e->getMessage(), RESPONSE::HTTP_FORBIDDEN);
            }
            $filesystem = new Filesystem();
            $filesystem->remove($videoFile->getPathname());

            return $this->redirectToRoute('index');
        }

        return $this->render('video/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/search', name: 'search_video')]
    public function search(Request $request)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->getData()['query'];

            $results = $this->videoService->getVideoByName($query);

            return $this->render('search/results.html.twig', [
                'results' => $results,
            ]);
        }

        return $this->render('search/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/video/{videoName}', name: 'video_watch')]
    public function watchVideo(Request $request, string $videoName)
    {
        $this->addView($videoName);

        $comment = [];

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();

            $session = $this->requestStack->getSession();
            $comment['session'] = $session;
            $comment['videoName'] = $videoName;

            $this->commentService->add($comment);

            return $this->redirectToRoute('video_watch', ['videoName' => $videoName]);
        }


        return $this->render('video/watch.html.twig', [
            'video' => $this->videoService->getVideo($videoName),
            'form' => $form->createView(),
            'comments' => $this->getVideoComments($videoName)
        ]);
    }

    public function getVideoComments(string $videoName)
    {
        $commentsJson = $this->commentService->getAll($videoName);
        $comments = json_decode($commentsJson, true);

        return $comments;
    }

    public function addView(string $videoName): void
    {
        $this->videoService->addViewToVideo($videoName);
    }
}
<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(private CommentService $commentService,
                                private RequestStack $requestStack)
    {
    }
    public function create(Request $request): Response
    {
        $comment = [];

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $comment = $form->getData();

            $session = $this->requestStack->getSession();
            $comment['session'] = $session;

            $this->commentService->add($comment);

            return $this->redirectToRoute('article_show', ['id' => $comment->getArticle()->getId()]);
        }

        return $this->render('comment/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
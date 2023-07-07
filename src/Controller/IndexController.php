<?php

namespace App\Controller;

use App\Service\VideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    public function __construct(private RequestStack   $requestStack,
                                private VideoService   $videoService)
    {
    }
    #[Route('/index', name: 'index')]
    public function index(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $recommendedVideos = $this->videoService->getRecommendedVideosByTagsViews($session);

        return $this->render('index.html.twig', [
            'recommendedVideos' => $recommendedVideos
        ]);
    }
}
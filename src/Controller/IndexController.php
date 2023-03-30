<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    public function __construct()
    {
    }
    #[Route('/index', name: 'index')]
    public function index(Request $request): Response
    {
        return $this->render('index.html.twig');
    }
}
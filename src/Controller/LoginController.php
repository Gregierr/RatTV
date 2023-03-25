<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserService;

class LoginController extends AbstractController
{
    public function __construct(private UserService $uS ){}

    #[Route('/index', name: 'index')]
    public function login(Request $request)
    {
        return $this->json("succes");
    }
}

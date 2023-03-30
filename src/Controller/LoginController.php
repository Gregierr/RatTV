<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\UserNotActiveException;
use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserService;
use App\Service\AuthenticationService;
use App\Exception\LoginFailedException;
use App\Exception\UserNotFoundException;
use App\Exception\UserAlreadyActiveException;

class LoginController extends AbstractController
{
    public function __construct(private AuthenticationService $authenticationService){}

    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            try {
                $this->authenticationService->checkIfPasswordIsValid($user);
            } catch (LoginFailedException $e) {
                return $this->json($e->getMessage(), RESPONSE::HTTP_FORBIDDEN);
            } catch (UserNotFoundException|UserNotActiveException $e) {
                return $this->json($e->getMessage(), $e->getCode());
            }
            return $this->json("success");
        }

        return $this->render('login.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

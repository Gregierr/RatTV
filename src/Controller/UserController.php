<?php

namespace App\Controller;

use App\Exception\UserAlreadyActiveException;
use App\Exception\UserNotFoundException;
use App\Service\AuthenticationService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    public function __construct(private UserService $userService,
                                private AuthenticationService $authenticationService)
    {

    }
    #[Route('/api/user/delete/{id}', name:'user_delete', methods: 'DELETE')]
    public function deleteUser(int $id) :JsonResponse
    {
        try{
            $this->userService->delete($id);
        }catch(UserNotFoundException $e){
            return $this->json($e->getMessage(), $e->getCode());
        }
        return $this->json("Success");
    }
    #[Route('/activationlink/{id}', name: 'activation_link')]
    public function getActivationLink(int $id): JsonResponse{
        try {
            $this->authenticationService->getActivationLink($id);
        }catch(UserNotFoundException $e){
            return $this->json($e->getMessage(), $e->getCode());
        }
        return $this->json("Success");
    }
    #[Route('/activate/{token}', name: 'activate')]
    public function activateUser(string $token): JsonResponse{
        try {
            $this->authenticationService->activateUser($token);
        }catch(UserAlreadyActiveException $e){
            return $this->json($e->getMessage(), $e->getCode());
        }
        return $this->json("Success");
    }
}
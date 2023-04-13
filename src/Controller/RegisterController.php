<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\ValidatorException;
use App\Form\RegisterType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class RegisterController extends AbstractController
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * @throws ExceptionInterface
     * @throws ValidatorException
     */
    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $user = [];
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $this->userService->add($user);

            return $this->json('Success');
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
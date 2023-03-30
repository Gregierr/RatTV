<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UserNotFoundException;
use App\Exception\LoginFailedException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Exception\UserAlreadyActiveException;
use App\Exception\UserNotActiveException;

class AuthenticationService
{
    public function __construct(private  EntityManagerInterface      $em,
                                private  UserPasswordHasherInterface $passwordHasher
    ){}

    /**
     * @throws UserNotFoundException
     * @throws LoginFailedException
     * @throws UserNotActiveException
     */
    public function checkIfPasswordIsValid($formUser): void
    {
        $login = $formUser->getLogin();
        $password = $formUser->getPassword();

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(["login" => $login]);

        if(!$user)
            throw new LoginFailedException();

        if(!$user->isActive())
            throw new UserNotActiveException();

        $this->passwordHasher->isPasswordValid($user, $password) ?: throw new LoginFailedException();
    }

    /**
     * @throws UserNotFoundException
     * @throws TransportExceptionInterface
     */
    public function getActivationLink(int $id): void
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy([
            'id' => $id,
            'isActive' => false,
            'isDeleted' => false
        ]);

        if(!$user)
            throw new UserNotFoundException($id);

        $token = random_int(PHP_INT_MIN, PHP_INT_MAX);
        $token = md5($token);

        $user->setActivationToken($token);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @throws UserAlreadyActiveException
     * @throws UserNotFoundException
     */
    public function activateUser($token): void
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy([
            "activationToken" => $token,
            "isDeleted" => false
        ]);

        if(!$user)
            throw new UserAlreadyActiveException();

        if($user->getActivationToken() == $token){
            $user->setIsActive(true);
            $user->setActivationToken(null);
        }

        $this->em->persist($user);
        $this->em->flush();
    }
}
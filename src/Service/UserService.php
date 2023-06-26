<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\LoginFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Exception\UserNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserService implements CrudInterface
{
    public function __construct(private EntityManagerInterface $em,
                                private UserPasswordHasherInterface $passwordHasher)
    {

    }

    public function add(array $data): void
    {
        $user = new User();
        $user->setLogin($data['login']);
        $user->setPassword($data['password']);
        $user->setEmail($data['email']);
        $user = $this->setHashedPassword($user, $user->getPassword());
        $user->setIsActive(false);
        $user->setIsDeleted(false);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @throws UserNotFoundException
     */
    public function delete(int $id): void
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)
            ->findOneBy(["id" => $id, "isDeleted" => false]);

        if(!$user)
            throw new UserNotFoundException($id);

        $user->setIsDeleted(true);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @throws UserNotFoundException
     * @throws LoginFailedException
     */
    public function update(int $id, array $data): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(["id" => $id]);

        if(!$this->passwordHasher->isPasswordValid($user, $data["password"]))
            throw new LoginFailedException();

        if(!$user)
            throw new UserNotFoundException($id);

        if(array_key_exists("email", $data))
            $this->updateEmail($user, $data["email"]);
        if(array_key_exists("login", $data))
            $this->updateLogin($user, $data["login"]);
        if(array_key_exists("new_password", $data))
            $this->updatePassword($user, $data["new_password"]);
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(int $id): User
    {
        /* @var User $user */
        $user = $this->em->getRepository(User::class)
            ->findOneBy(["id" => $id]);

        if(!$user)
            throw new UserNotFoundException($id);

        return $user;
    }

    public function getAll(): array
    {
        $allUsers = [];
        /** @var User[] $users */
        $users = $this->em->getRepository(User::class)->findBy(["isDeleted" => false]);

        foreach($users as $user)
        {
            $allUsers[] = [
                "login" => $user->getLogin(),
                "email" => $user->getEmail(),
            ];
        }
        return $allUsers;
    }
    public function updateEmail($user, $email)
    {
        $user->setEmail($email);

        $this->em->persist($user);
        $this->em->flush();;
    }
    public function updateLogin($user, $login)
    {
        $user->setLogin($login);

        $this->em->persist($user);
        $this->em->flush();;
    }
    public function updatePassword($user, $newPassword)
    {
        $user = $this->setHashedPassword($user, $newPassword);

        $this->em->persist($user);
        $this->em->flush();

    }
    public function setHashedPassword($user, $plainTextPassword)
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainTextPassword
        );
        $user->setPassword($hashedPassword);
        return $user;
    }

}
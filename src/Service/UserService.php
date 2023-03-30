<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\ValidatorException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Exception\UserNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserService implements CrudInterface
{
    public function __construct(private EntityManagerInterface $em,
                                private UserPasswordHasherInterface $passwordHasher,
                                private ValidatorInterface $validator)
    {

    }

    /**
     * @throws ExceptionInterface
     * @throws ValidatorException
     */
    public function add(User $user): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);
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
    public function update(int $id, array $data)
    {

    }
    public function get(int $id )
    {
        $user = $this->em->getRepository(User::class)
            ->findOneBy(["id" => $id]);
        return $user;
    }

}
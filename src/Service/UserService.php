<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService 
{
    public function __construct(private EntityManagerInterface $em)
    {

    }
    public function add()
    {

    }
    public function delete()
    {

    }
    public function update()
    {

    }
    public function get(int $id )
    {
        $user = $this->em->getRepository(User::class)
            ->findOneBy(["id" => $id]);
        return $user;
    }

}
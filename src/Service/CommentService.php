<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\Cloner\Data;

class CommentService implements CrudInterface
{
    public function __construct(private EntityManagerInterface $em,
                                )
    {
    }
public function add(array $data)
{
    $session = $data['session'];

    $user = new User();
    $user = $this->em->getRepository(User::class)->findOneBy(['id' => $session->get("id")]);

    $video = new Video();
    $video = $this->em->getRepository(Video::class)->findOneBy(['videoName' => $data['videoName']]);

    $comment = new Comment();
    $comment->setText($data['text']);
    $comment->addUser($user);
    $comment->setUploadDate(new \DateTime('now'));
    $comment->setVideo($video);

    $this->em->persist($comment);
    $this->em->flush();
}public function delete(int $id)
{
    // TODO: Implement delete() method.
}public function update(int $id, array $data)
{
    // TODO: Implement update() method.
}public function get(int $id)
{
    // TODO: Implement get() method.
}}
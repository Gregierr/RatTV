<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class VideoService
{
    public function __construct(private EntityManagerInterface $em,
                                private VideoRepository $videoRepository)
    {
    }
    public function saveVideo($filename, $id, $title)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(["id" =>$id, "isDeleted" => false]);

        if(!$user)
            throw new AccessDeniedException();
        $video = new Video();
        $video->setVideoName($filename);
        $video->setUploadDate(new \DateTime('now'));
        $video->setUser($user);
        $video->setTitle($title);

        $this->em->persist($video);
        $this->em->flush();
    }
    public function getVideo(int $id)
    {
        return $this->em->getRepository(Video::class)->findOneBy(["id"=> $id]);
    }
    public function getVideoByName($keyword)
    {
        return $this->videoRepository->findVideoByKeyword($keyword);
    }
}
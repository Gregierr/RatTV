<?php

namespace App\Service;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Video;
use App\Exception\VideoNotFoundException;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class VideoService
{
    public function __construct(private EntityManagerInterface $em,
                                private VideoRepository        $videoRepository)
    {
    }

    public function saveVideo($filename, $id, $form): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(["id" => $id, "isDeleted" => false]);

        if (!$user)
            throw new AccessDeniedException();
        $video = new Video();
        $video->setVideoName($filename);
        $video->setUploadDate(new \DateTime('now'));
        $video->setUser($user);
        $video->setTitle($form->get('title')->getData());

        $tagNames = $form->get('tags')->getData();
        $tags = [];

        foreach ($tagNames as $tagName) {
            $tag = $this->em->getRepository(Tag::class)->findOneBy(['name' => $tagName]);

            if ($tag === null) {
                $tag = new Tag();
                $tag->setName($tagName);
                $this->em->persist($tag);
            }

            $video->addTag($tag);
        }

        $this->em->persist($video);
        $this->em->flush();
    }

    public function getVideo(string $videoName): Video
    {
        return $this->em->getRepository(Video::class)->findOneBy(["videoName" => $videoName]);
    }

    public function getVideoByName($keyword): Video
    {
        return $this->videoRepository->findVideoByKeyword($keyword);
    }

    public function addViewToVideo(string $videoName): void
    {
        /* @var Video $video */
        $video = $this->em->getRepository(Video::class)->findOneBy(["videoName" => $videoName]);

        $video->setViews($video->getViews() + 1);

        $this->em->persist($video);
        $this->em->flush();
    }
}
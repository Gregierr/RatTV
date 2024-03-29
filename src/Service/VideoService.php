<?php

namespace App\Service;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\UserTag;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class VideoService
{
    public function __construct(private EntityManagerInterface $em,
                                private VideoRepository        $videoRepository,
                                private CommentService         $commentService)
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
        $video->setTitle($form['title']);

        $tagNames = $form['tags'];

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

    public function getVideoByName($keyword): array
    {
        return $this->videoRepository->findVideoByKeyword($keyword);
    }

    public function addViewToVideo(string $videoName): void
    {
        $video = $this->getVideo($videoName);

        $video->setViews($video->getViews() + 1);

        $this->em->persist($video);
        $this->em->flush();
    }

    public function addWatchedTagsToUser($session, string $videoName): void
    {
        /* @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $session->get("id")]);

        $video = $this->getVideo($videoName);

        $tags = $video->getTags();

        foreach ($tags as $tag) {
            $userTag = $this->em->getRepository(UserTag::class)->findOneBy(['user' => $user, 'tag' => $tag]);

            if ($userTag === null) {

                $userTag = new UserTag();
                $userTag->setUser($user);
                $userTag->setTag($tag);
                $userTag->setViewCount(1);
                $user->addUserTag($userTag);
                $this->em->persist($userTag);
            } else {
                $userTag->setViewCount($userTag->getViewCount() + 1);
            }

            $this->em->persist($userTag);
        }

        $this->em->flush();
    }

    public function getRecommendedVideosByTagsViews($session): array
    {
        /* @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $session->get("id")]);

        $mostViewedTag = $this->em->getRepository(UserTag::class)->getMostViewedTagForUser($user);

        if ($mostViewedTag === null) {
            return $this->em->getRepository(Video::class)->findBy([], ['views' => 'DESC'], 3);
        }

        $videosWithTag = $this->em->getRepository(Video::class)->getVideosWithTag($mostViewedTag);

        usort($videosWithTag, function (Video $a, Video $b) {
            return $b->getViews() - $a->getViews();
        });

        return array_slice($videosWithTag, 0, 3);
    }

    public function getVideoComments(string $videoName): ?array
    {
        $commentsJson = $this->commentService->getAll($videoName);
        $comments = json_decode($commentsJson, true);

        return $comments;
    }

    public function addView(string $videoName): void
    {
        $this->addViewToVideo($videoName);
    }
}
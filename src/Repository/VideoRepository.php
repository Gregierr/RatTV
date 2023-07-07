<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 *
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function save(Video $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Video $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findVideoByKeyword($keyword)
    {
        $qb = $this->createQueryBuilder('v');

        $words = preg_split('/\s+/', $keyword, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $index => $word) {
            $qb->orWhere("v.title LIKE :word{$index}")
                ->setParameter("word{$index}", "%{$word}%");
        }

        return $qb->getQuery()->getResult();
    }

    public function getVideosWithTag(Tag $tag): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.tags', 't')
            ->andWhere('t.id = :tagId')
            ->setParameter('tagId', $tag->getId())
            ->orderBy('v.views', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
}

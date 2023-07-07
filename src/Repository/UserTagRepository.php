<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\UserTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserTag>
 *
 * @method UserTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTag[]    findAll()
 * @method UserTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTag::class);
    }

    public function save(UserTag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserTag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getMostViewedTagForUser($user): ?Tag
    {
        $tagId = $this->createQueryBuilder('ut')
            ->select('IDENTITY(ut.tag) as tagId, COUNT(ut.tag) as HIDDEN tagCount')
            ->andWhere('ut.user = :user')
            ->setParameter('user', $user)
            ->groupBy('tagId')
            ->orderBy('tagCount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->getEntityManager()->getRepository(Tag::class)->find($tagId);
    }
}

<?php

namespace App\Repository;

use App\Entity\BadgeForMediaTimeline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BadgeForMediaTimeline>
 *
 * @method BadgeForMediaTimeline|null find($id, $lockMode = null, $lockVersion = null)
 * @method BadgeForMediaTimeline|null findOneBy(array $criteria, array $orderBy = null)
 * @method BadgeForMediaTimeline[]    findAll()
 * @method BadgeForMediaTimeline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadgeForMediaTimelineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BadgeForMediaTimeline::class);
    }

//    /**
//     * @return BadgeForMediaTimeline[] Returns an array of BadgeForMediaTimeline objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BadgeForMediaTimeline
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

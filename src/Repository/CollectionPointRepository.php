<?php

namespace App\Repository;

use App\Entity\CollectionPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CollectionPoint>
 *
 * @method CollectionPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectionPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectionPoint[]    findAll()
 * @method CollectionPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectionPoint::class);
    }

//    /**
//     * @return CollectionPoint[] Returns an array of CollectionPoint objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CollectionPoint
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\ConditionOccasion;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ConditionBox>
 *
 * @method ConditionBox|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConditionBox|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConditionBox[]    findAll()
 * @method ConditionBox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConditionOccasionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConditionOccasion::class);
    }

//    /**
//     * @return ConditionOccasion[] Returns an array of ConditionBox objects
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

//    public function findOneBySomeField($value): ?ConditionBox
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

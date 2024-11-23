<?php

namespace App\Repository;

use App\Entity\DocumentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentStatus>
 *
 * @method DocumentStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentStatus[]    findAll()
 * @method DocumentStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentStatus::class);
    }

    public function findStatusIsTraitedDaily(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.isToBeTraitedDaily = :val')
            ->setParameter('val', true)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return DocumentStatus[] Returns an array of DocumentStatus objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DocumentStatus
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

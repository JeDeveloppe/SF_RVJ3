<?php

namespace App\Repository;

use App\Entity\DocumentLineTotals;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentLineTotals>
 *
 * @method DocumentLineTotals|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentLineTotals|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentLineTotals[]    findAll()
 * @method DocumentLineTotals[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentLineTotalsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentLineTotals::class);
    }

//    /**
//     * @return DocumentLineTotals[] Returns an array of DocumentLineTotals objects
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

//    public function findOneBySomeField($value): ?DocumentLineTotals
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

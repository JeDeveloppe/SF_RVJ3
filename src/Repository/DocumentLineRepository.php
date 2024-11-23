<?php

namespace App\Repository;

use App\Entity\DocumentLine;
use App\Entity\Occasion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentLine>
 *
 * @method DocumentLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentLine[]    findAll()
 * @method DocumentLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentLine::class);
    }

    public function countTotalOfItemsBilled(): int
    {
        return $this->createQueryBuilder('d')
            ->where('d.boite IS NOT NULL') // comptage V2
            ->orWhere('d.item IS NOT NULL') //comptage V3
            ->select('count(d.item) + count(d.boite)')
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }

//    /**
//     * @return DocumentLine[] Returns an array of DocumentLine objects
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

//    public function findOneBySomeField($value): ?DocumentLine
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

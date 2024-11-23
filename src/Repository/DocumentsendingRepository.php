<?php

namespace App\Repository;

use App\Entity\Documentsending;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentsending>
 *
 * @method Documentsending|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentsending|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentsending[]    findAll()
 * @method Documentsending[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentsendingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentsending::class);
    }

//    /**
//     * @return Documentsending[] Returns an array of Documentsending objects
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

//    public function findOneBySomeField($value): ?Documentsending
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

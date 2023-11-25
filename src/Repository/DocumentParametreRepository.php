<?php

namespace App\Repository;

use App\Entity\DocumentParametre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentParametre>
 *
 * @method DocumentParametre|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentParametre|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentParametre[]    findAll()
 * @method DocumentParametre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentParametreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentParametre::class);
    }

//    /**
//     * @return DocumentParametre[] Returns an array of DocumentParametre objects
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

//    public function findOneBySomeField($value): ?DocumentParametre
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

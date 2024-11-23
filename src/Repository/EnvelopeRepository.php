<?php

namespace App\Repository;

use App\Entity\Envelope;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Envelope>
 *
 * @method Envelope|null find($id, $lockMode = null, $lockVersion = null)
 * @method Envelope|null findOneBy(array $criteria, array $orderBy = null)
 * @method Envelope[]    findAll()
 * @method Envelope[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvelopeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Envelope::class);
    }

//    /**
//     * @return Envelope[] Returns an array of Envelope objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Envelope
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

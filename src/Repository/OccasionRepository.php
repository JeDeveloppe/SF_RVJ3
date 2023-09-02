<?php

namespace App\Repository;

use App\Entity\Occasion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Occasion>
 *
 * @method Occasion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Occasion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Occasion[]    findAll()
 * @method Occasion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OccasionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Occasion::class);
    }

//    /**
//     * @return Occasion[] Returns an array of Occasion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Occasion
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

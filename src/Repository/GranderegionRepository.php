<?php

namespace App\Repository;

use App\Entity\Granderegion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Granderegion>
 *
 * @method Granderegion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Granderegion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Granderegion[]    findAll()
 * @method Granderegion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GranderegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Granderegion::class);
    }

//    /**
//     * @return Granderegion[] Returns an array of Granderegion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Granderegion
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\MeansOfPayement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MeansOfPayement>
 *
 * @method MeansOfPayement|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeansOfPayement|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeansOfPayement[]    findAll()
 * @method MeansOfPayement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeansOfPayementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeansOfPayement::class);
    }

//    /**
//     * @return MeansOfPayement[] Returns an array of MeansOfPayement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MeansOfPayement
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

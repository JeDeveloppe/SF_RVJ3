<?php

namespace App\Repository;

use App\Entity\DurationOfGame;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<DurationOfGame>
 *
 * @method DurationOfGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method DurationOfGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method DurationOfGame[]    findAll()
 * @method DurationOfGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DurationOfGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DurationOfGame::class);
    }

//    /**
//     * @return DurationOfGame[] Returns an array of DurationOfGame objects
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

//    public function findOneBySomeField($value): ?DurationOfGame
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

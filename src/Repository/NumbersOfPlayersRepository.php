<?php

namespace App\Repository;

use App\Entity\NumbersOfPlayers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NumbersOfPlayers>
 *
 * @method NumbersOfPlayers|null find($id, $lockMode = null, $lockVersion = null)
 * @method NumbersOfPlayers|null findOneBy(array $criteria, array $orderBy = null)
 * @method NumbersOfPlayers[]    findAll()
 * @method NumbersOfPlayers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NumbersOfPlayersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NumbersOfPlayers::class);
    }

//    /**
//     * @return NumbersOfPlayers[] Returns an array of NumbersOfPlayers objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NumbersOfPlayers
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

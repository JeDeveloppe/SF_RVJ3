<?php

namespace App\Repository;

use App\Entity\MovementOccasion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MovementOccasion>
 *
 * @method MovementOccasion|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovementOccasion|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovementOccasion[]    findAll()
 * @method MovementOccasion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovementOccasionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovementOccasion::class);
    }

//    /**
//     * @return MovementOccasion[] Returns an array of MovementOccasion objects
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

//    public function findOneBySomeField($value): ?MovementOccasion
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

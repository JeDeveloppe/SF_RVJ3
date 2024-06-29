<?php

namespace App\Repository;

use App\Entity\CollectionPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CollectionPoint>
 *
 * @method CollectionPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectionPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectionPoint[]    findAll()
 * @method CollectionPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectionPoint::class);
    }

    public function findOneCollectionPointForOccasionBuy(): ?CollectionPoint
    {
        $name = $_ENV['SHIPPING_METHOD_BY_IN_RVJ_DEPOT_NAME'];

        return $this->createQueryBuilder('c')
            ->join('c.shippingmethod', 's')
            ->where('s.name = :name')
            ->andWhere('c.isOriginForWebSiteCmds = :val')
            ->setParameter('val', true)
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return CollectionPoint[] Returns an array of CollectionPoint objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CollectionPoint
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

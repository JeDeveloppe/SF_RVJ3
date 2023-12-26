<?php

namespace App\Repository;

use App\Entity\VoucherDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VoucherDiscount>
 *
 * @method VoucherDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method VoucherDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method VoucherDiscount[]    findAll()
 * @method VoucherDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoucherDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VoucherDiscount::class);
    }

//    /**
//     * @return VoucherDiscount[] Returns an array of VoucherDiscount objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VoucherDiscount
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

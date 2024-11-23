<?php

namespace App\Repository;

use App\Entity\CatalogOccasionSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CatalogOccasionSearch>
 *
 * @method CatalogOccasionSearch|null find($id, $lockMode = null, $lockVersion = null)
 * @method CatalogOccasionSearch|null findOneBy(array $criteria, array $orderBy = null)
 * @method CatalogOccasionSearch[]    findAll()
 * @method CatalogOccasionSearch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogOccasionSearchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogOccasionSearch::class);
    }

//    /**
//     * @return CatalogOccasionSearch[] Returns an array of CatalogOccasionSearch objects
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

//    public function findOneBySomeField($value): ?CatalogOccasionSearch
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

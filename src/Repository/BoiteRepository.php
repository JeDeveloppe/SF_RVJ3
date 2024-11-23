<?php

namespace App\Repository;

use App\Entity\Boite;
use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Boite>
 *
 * @method Boite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Boite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Boite[]    findAll()
 * @method Boite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boite::class);
    }

    public function findDistinctEditors(): array
    {
        return $this->createQueryBuilder('b')
            ->select('b.initeditor')
            ->groupBy('b.initeditor')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findItemsFromBoitesFromSearch($phrase): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.editor','e')
            ->where('b.name LIKE :val')
            ->orWhere('e.name LIKE :val')
            ->andWhere('b.isOnline = :online')
            ->setParameter('val', '%'.$phrase.'%')
            ->setParameter('online', true)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBoitesWhereThereIsItems()
    {

        $results =  $this->createQueryBuilder('b')
            ->where('b.isOnline = :true')
            ->join('b.itemsOrigine', 'i')
            ->andWhere('i.stockForSale > :minimum')
            ->setParameter('true', true)
            ->setParameter('minimum', 0)
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $donnees = [];

        foreach($results as $donneesFromDatabase){
            if(count($donneesFromDatabase->getItemsOrigine()) > 0 OR count($donneesFromDatabase->getItemsSecondaire()) > 0){

                array_push($donnees,$donneesFromDatabase);

            }
        }

        return $donnees;
    }


//    /**
//     * @return Boite[] Returns an array of Boite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Boite
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

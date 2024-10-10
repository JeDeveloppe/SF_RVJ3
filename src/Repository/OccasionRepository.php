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

    public function searchOccasionsInCatalogue(
        string $searchName,
        array $ageStarts = null,
        int $ageEnd = null,
        array $players,
        array $durations,
        array $choices): array
    {

        if(count($players) == 0){
            $players = [];
            foreach($choices['players_options_for_form'] as $choice){
                $players[] = $choice;
            }
        }
        
        if(count($players) == 1 && $players[0] == 6){
            $players = [];
            foreach($choices['players_in_database'] as $choice){
                if($choice->getName() > 5){
                    $players[] = $choice;
                }
            }
        }

        if(count($durations) == 0){
            foreach($choices['durations_options_for_form'] as $choice){
                $durations[] = $choice;
            }
        }

        if(count($ageStarts) == 0){
            foreach($choices['ages_options_for_form'] as $choice){
                $ageStarts[] = $choice;
            }
        }

        $query =  $this->createQueryBuilder('o')
            ->join('o.boite','b')
            ->join('b.editor','e')
            ->join('b.durationGame','d')
            ->join('o.stock','s')
            ->where('b.name LIKE :searchName')
            ->where('s.name = :stock_name')
            ->orWhere('e.name LIKE :searchName')
            ->andWhere('b.playersMin IN (:players)')
            // ->orWhere('b.playersMax >= (:players)')
            ->andWhere('d.name IN (:durations)')
            ->andWhere('b.age IN (:ageStarts)')
            ->andWhere('o.isOnline = :online')
            ->setParameters([
                'searchName' => '%'.$searchName.'%',
                'players' => $players,
                'durations' => $durations,
                'ageStarts' => $ageStarts,
                'online' =>  true,
                'stock_name' => 'SUR LE SITE'
            ])
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        
        return $query;
    }

    public function searchAllOccasionsByStartAndEndFromCategory(int $age_start, int $age_end): array
    {

        return $this->createQueryBuilder('o')
            ->join('o.boite','b')
            ->join('o.stock','s')
            ->where('b.age >= :age_start')
            ->andWhere('s.name = :stock_name')
            ->andWhere('b.age <= :age_end')
            ->andWhere('o.isOnline = :online')
            ->setParameters([
                'age_start' => $age_start,
                'age_end' => $age_end,
                'online' =>  true,
                'stock_name' => 'SUR LE SITE'
            ])
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAleatoireOccasionsByAgeWhitoutThisOccasion(int $age, Occasion $occasion): array
    {

        return $this->createQueryBuilder('o')
            ->join('o.boite','b')
            ->join('o.stock','s')
            ->where('o.isOnline = :online')
            ->andWhere('s.name = :stock_name')
            ->andWhere('b.age = :age')
            ->andWhere('o.id != :occasionId')
            ->setParameters([
                'occasionId' => $occasion->getId(),
                'age' => $age,
                'online' =>  true,
                'stock_name' => 'SUR LE SITE'
            ])
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->setMaxResults(20)
            ->getResult()
        ;
    }

    public function findUniqueOccasionByRefrenceWhenIsOnLineAndSlugIsOk(string $occasionReference, string $boiteSlug)
    {

        return $this->createQueryBuilder('o')
        ->join('o.boite','b')
        ->where('b.slug = :slug')
        ->andWhere('o.reference = :occasionReference')
        ->andWhere('o.isOnline = :online')
        ->setParameters([
            'slug' => $boiteSlug,
            'occasionReference' => $occasionReference,
            'online' =>  true,
        ])
        ->getQuery()
        ->getSingleResult()
        ;

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

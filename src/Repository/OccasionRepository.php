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

    public function searchOccasionsByNameOrEditorInCatalogue(
        string $searchName,
        int $ageStart = null,
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

        if(count($durations) == 0){
            foreach($choices['durations_options_for_form'] as $choice){
                $durations[] = $choice;
            }
        }

        if($ageStart > 0){
            $signe_age = "= :age_start";
            $value_age = $ageStart;
        }else{
            $signe_age = ">= :age_start";
            $value_age = 0;
        }

        //s'il n'y une durée de partie
            $query =  $this->createQueryBuilder('o')
                ->join('o.boite','b')
                ->join('b.editor','e')
                ->join('b.durationGame','d')
                ->where('b.name LIKE :searchName')
                ->orWhere('e.name LIKE :searchName')
                ->andWhere('b.playersMin IN (:players)')
                ->andWhere('b.playersMax IN (:players)')
                ->andWhere('d.name IN (:durations)')
                ->andWhere('b.age '.$signe_age)
                ->andWhere('b.age <= :age_end')
                ->andWhere('o.isOnline = :online')
                ->setParameters([
                    'searchName' => '%'.$searchName.'%',
                    'players' => $players,
                    'durations' => $durations,
                    'age_start' => $value_age,
                    'age_end' => $ageEnd,
                    'online' =>  true,
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
            ->where('b.age >= :age_start')
            ->andWhere('b.age <= :age_end')
            ->andWhere('o.isOnline = :online')
            ->setParameters([
                'age_start' => $age_start,
                'age_end' => $age_end,
                'online' =>  true,
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
            ->where('o.isOnline = :online')
            ->andWhere('b.age >= :age')
            ->andWhere('o.id != :occasionId')
            ->setParameters([
                'occasionId' => $occasion->getId(),
                'age' => $age,
                'online' =>  true,
            ])
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->setMaxResults(20)
            ->getResult()
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

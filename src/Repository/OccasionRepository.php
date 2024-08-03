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

    public function searchOccasionsByNameOrEditorInCatalogue(string $phrase, int $age_start, int $age_end, array $players): array
    {

        return $this->createQueryBuilder('o')
            ->join('o.boite','b')
            ->join('b.editor','e')
            ->orWhere('b.name LIKE :phrase')
            ->orWhere('e.name LIKE :phrase')
            // ->andWhere('b.playersMin >= :players') //TODO if NULL
            ->andWhere('b.age >= :age_start')
            ->andWhere('b.age <= :age_end')
            ->andWhere('o.isOnline = :online')
            ->setParameters([
                'phrase' => '%'.$phrase.'%',
                'age_start' => $age_start,
                'age_end' => $age_end,
                // 'players' => $players, //TODO
                'online' =>  true,
            ])
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchAllOccasions(int $age_start, int $age_end): array
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
            ->orderBy('b.id', 'ASC')
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

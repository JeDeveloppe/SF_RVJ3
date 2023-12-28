<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 *
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function findDocumentsToBeTraitedDailyWithStatus($status): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.documentStatus = :status')
            ->setParameter('status', $status)
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastEntryFromThisYear($column, $year)
    {
        return $this->createQueryBuilder('d')
            ->where('YEAR(d.createdAt) = :year')
            ->andWhere('d.'.$column.' IS NOT NULL')
            ->setParameter('year', $year)
            ->orderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByDevisToDelete($now){

        return $this->createQueryBuilder('d')
            ->where('d.endOfQuoteValidation < :now')
            ->andWhere('d.billNumber IS NULL')
            ->andWhere('d.isQuoteReminder = :value') //devis bien relancer par email, on a donc remis X jours
            ->setParameter('now', $now)
            ->setParameter('value', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByDevisToReminder($now){

        return $this->createQueryBuilder('d')
            ->where('d.endOfQuoteValidation < :now')
            ->andWhere('d.billNumber IS NULL')
            ->andWhere('d.isQuoteReminder = :value')
            ->setParameter('now', $now)
            ->setParameter('value', false)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByDocumentWithPaiementInYear(string $billTag, int $year = null){

        if(is_int($year)){
            $year = substr($year, -2);
        }

        return $this->createQueryBuilder('d')
            ->where('d.billNumber LIKE :docstart')
            ->setParameter('docstart', $billTag.$year.'%') //only 23 in 2023
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Document[] Returns an array of Document objects
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

//    public function findOneBySomeField($value): ?Document
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Service;

use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentLineRepository;

class AdminService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DocumentLineRepository $documentLineRepository,
        private OccasionRepository $occasionRepository
    ) {}

    public function updateOccasionsLogic()
    {

        //? on recupere dans un tableau les lignes des documents
        $documentsLines = $this->documentLineRepository->findAll();

        foreach ($documentsLines as $documentLine) {
            $occasion = $documentLine->getOccasion();

            if ($occasion) {
                //les occasions vendu sont mis hors ligne et hors reserve
                $occasion->setIsOnline(false)->setIsBilled(true)->setIsReserved(false);
                $this->em->persist($occasion);
            }
        }
        $this->em->flush();


        //?on recupere les occasions en ligne
        $occasions = $this->occasionRepository->findBy(['isOnline' => true]);

        foreach ($occasions as $occasion) {
            //les occasions vendu sont mis hors vente et hors reserve
            $occasion->setIsBilled(false)->setIsReserved(false);
            $this->em->persist($occasion);
        }
        $this->em->flush();


        //?on recupere les occasion hors ligne, ni vendu, ni reserve
        $occasions = $this->occasionRepository->returnAllOccasionsOfflineAndBilledIsNullAndReservedIsNull();
        $occasionsOffLineReservedNullAndBIlledNull = []; //on mettra dans un tableau les non donner / vendu hors site
        foreach ($occasions as $occasion) {
            if($occasion->getOffSiteOccasionSale()) {
                //si occasion vendu hors du site, il n'est plus en ligne, il est vendu, il n'est plus en reserve
                $occasion->setIsOnline(false)->setIsBilled(true)->setIsReserved(false)->setReserve(null);
                $this->em->persist($occasion);

            }else{
                $occasionsOffLineReservedNullAndBIlledNull[] = $occasion;
            }
        }
        $this->em->flush();

        //on traite le tableau des non vendu / non donner
        foreach ($occasionsOffLineReservedNullAndBIlledNull as $occasion) {
            $occasion->setIsOnline(false)->setIsBilled(false)->setIsReserved(false)->setReserve(null);
            $this->em->persist($occasion);
        }
        $this->em->flush();

    }
}

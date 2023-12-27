<?php

namespace App\Service;

use App\Repository\LegalInformationRepository;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class VoucherDiscountService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailService $mailService,
        private Security $security,
        private LegalInformationRepository $legalInformationRepository
        )
    {
    }

    public function saveVoucherDiscountInDatabaseAndSendEmail($voucherDiscount){

        $user = $this->security->getUser();
        $date = $voucherDiscount->getValidUntil();
        $validUntil = $date->setTime(23, 59, 59);
        $voucherDiscount
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setUuid(Uuid::v4())
            ->setUsed(false)
            ->setCreatedBy($user)
            ->setValidUntil($validUntil);

        $legales = $this->legalInformationRepository->findOneBy([]);

        $donnees = [
            'donnees' => $voucherDiscount,
            'legales' => $legales
        ];

        $this->em->persist($voucherDiscount);
        $this->em->flush();

        $this->mailService->sendMail($voucherDiscount->getEmail(),'Bon d\'achat sur Refaites Vos Jeux', 'voucher_discount', $donnees, null, false);
    }

}
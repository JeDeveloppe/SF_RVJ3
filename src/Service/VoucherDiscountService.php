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
        $remainingValueToUseExcludingTax = $voucherDiscount->getDiscountValueExcludingTax();
        $validUntil = $date->setTime(23, 59, 59);
        $now = new DateTimeImmutable('now');
        $token = $now->format('my').'-'.$now->getTimestamp();

        $voucherDiscount
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setRemainingValueToUseExcludingTax($remainingValueToUseExcludingTax)
            ->setToken($token)
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

        $this->mailService->sendMail(true, $voucherDiscount->getEmail(),'Bon d\'achat sur Refaites Vos Jeux', 'voucher_discount', $donnees, null, false);
    }

}
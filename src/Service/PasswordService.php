<?php

namespace App\Service;

use App\Repository\LegalInformationRepository;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;


class PasswordService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailService $mailService,
        private LegalInformationRepository $legalInformationRepository
        ){
    }

    public function saveResetPasswordInDatabaseAndSendEmail($resetPassword){

        $resetPassword->setCreatedAt(new DateTimeImmutable('now'))->setUuid(Uuid::v4())->setIsUsed(false);

        $donnees = [
            'recipient' => $resetPassword->getEmail(),
            'uuid' => $resetPassword->getUuid(),
            'legales' => $this->legalInformationRepository->findOneBy([])
        ];

        $this->em->persist($resetPassword);
        $this->em->flush();

        $this->mailService->sendMail(true, $resetPassword->getEmail(),'Lien pour le changement de votre mot de passe', 'reset_password', $donnees, null, false);
    }

}
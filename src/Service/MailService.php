<?php

namespace App\Service;

use App\Repository\LegalInformationRepository;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

//TODO
class MailService
{
    public function __construct(
        private MailerInterface $mailer,
        private LegalInformationRepository $legalInformationRepository
        ){
    }

    public function sendMail($recipient, $subject, $template, array $donnees = null){

        if(is_null($donnees)){
            $donnees = [];
        }

        $legales = $this->legalInformationRepository->findOneBy([]);

        $mail = (new TemplatedEmail())
            ->from(new Address($legales->getEmailCompany(), $legales->getCompanyName()))
            ->to($recipient)
            ->replyTo('no_reply@lesapef.fr')
            ->subject($subject)
            ->htmlTemplate('email/templates/'.$template.'.html.twig')
            ->context($donnees);

        try{
            $this->mailer->send($mail);
        } catch (TransportExceptionInterface $e) {
            dump($e->getDebug());
        }
    }
}
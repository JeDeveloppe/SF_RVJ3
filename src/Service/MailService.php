<?php

namespace App\Service;

use DateInterval;
use Symfony\Component\Mime\Address;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\LegalInformationRepository;
use App\Repository\DocumentParametreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

//TODO montrer les differents email de l'application
//email changement statut du document aussi
class MailService
{
    public function __construct(
        private MailerInterface $mailer,
        private LegalInformationRepository $legalInformationRepository,
        private DocumentParametreRepository $documentParametreRepository,
        private EntityManagerInterface $em
        ){
    }

    public function sendMail($recipient, $subject, $template, array $donnees = null, $replyTo = null){

        if(is_null($donnees)){
            $donnees = [];
        }

        $legales = $this->legalInformationRepository->findOneBy([]);

        $mail = (new TemplatedEmail())
            ->from(new Address($legales->getEmailCompany(), $legales->getCompanyName()))
            ->to($recipient)
            ->replyTo($replyTo ? $replyTo : 'no_reply@refaitesvosjeux.fr')
            ->subject($subject)
            ->htmlTemplate('email/templates/'.$template.'.html.twig')
            ->context($donnees);

        try{
            $this->mailer->send($mail);
        } catch (TransportExceptionInterface $e) {
            dump($e->getDebug());
        }
    }

    public function reminderQuotes(array $documents, $now){

        foreach($documents as $document){

            //on cherche les parametres des documents
            $docParams = $this->documentParametreRepository->findOneBy(['isOnline' => true]);
            $legales = $this->legalInformationRepository->findOneBy([]);

            $endDevis = $now->add(new DateInterval('P'.$docParams->getDelayBeforeDeleteDevis().'D'));
            $document->setEndOfQuoteValidation($endDevis)->setIsQuoteReminder(true);
            $this->em->persist($document);

            $this->sendMail(
                $document->getUser()->getEmail(),
                'Devis '.$document->getQuoteNumber().' en attente...',
                'reminder_quote', [
                    'document' => $document,
                    'endDevis' => $document->getEndOfQuoteValidation(),
                    'docParams' => $docParams,
                    'legales' => $legales
                    ]
            );

        }

        $this->em->flush();
    }
}
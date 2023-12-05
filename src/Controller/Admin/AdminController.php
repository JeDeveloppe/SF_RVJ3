<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentStatusRepository;
use App\Repository\PaymentRepository;
use App\Service\MailService;
use App\Service\PaiementService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private EntityManagerInterface $em,
        private PaymentRepository $paymentRepository,
        private MailService $mailService,
        private PaiementService $paiementService,
        private UserRepository $userRepository
    )
    {
    }

    #[Route('/admin/change-status-of-document/{document}/{status}', name: 'admin_change_status_document')]
    public function changeStatusDocument($document,$status): Response
    {
        $document = $this->documentRepository->findOneBy(['token' => $document]);
        $status = $this->documentStatusRepository->findOneby(['action' => $status]);

        if(!$document || !$status){

            $this->addFlash('warning', 'Document inconnu !');
            return $this->redirectToRoute('admin_traited_daily_commands');

        }else{

            //TODO envoi email
            //$this->emailService->sendEmailWhenDocumentIsChangingStatus($document,$status);

            $document->setDocumentStatus($status);
            $this->em->persist($document);
            $this->em->flush();

            $this->addFlash('success', 'Status mis à jour !');
            return $this->redirectToRoute('admin_traited_daily_commands');

        }
    }

    #[Route('/admin/details-payment/{token}', name: 'admin_invoice_details')]
    public function paymentDetails($token){

        $payment = $this->paymentRepository->findOneBy(['tokenPayment' => $token]);

        $this->paiementService->payplugAuth();

        $result = \Payplug\Payment::retrieve($token);

        return $this->render('admin/details_payment.html.twig', [
            'payment' => $payment,
            'result' => $result
        ]);
    }

}

<?php

namespace App\Controller\Admin;

use App\Form\AddressType;
use App\Form\ManualInvoiceType;
use App\Repository\UserRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentStatusRepository;
use App\Repository\LegalInformationRepository;
use App\Repository\PaymentRepository;
use App\Repository\ReserveRepository;
use App\Repository\SiteSettingRepository;
use App\Service\DocumentService;
use App\Service\MailService;
use App\Service\PaiementService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class AdminController extends AbstractController
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private DocumentService $documentService,
        private EntityManagerInterface $em,
        private PaymentRepository $paymentRepository,
        private LegalInformationRepository $legalInformationRepository,
        private MailService $mailService,
        private PaiementService $paiementService,
        private SiteSettingRepository $siteSettingRepository,
        private UserRepository $userRepository,
        private ReserveRepository $reserveRepository
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

            $this->documentService->statusChange($document,$status);

            $this->addFlash('success', 'Status mis à jour !');
            return $this->redirectToRoute('admin_traited_daily_commands');

        }
    }

    #[Route('/admin/change-status-of-option/{option}/{value}', name: 'admin_change_status_option')]
    public function changeStatusOption($option,$value, Request $request): Response
    {

        $setting = $this->siteSettingRepository->findOneBy([]);

        switch ($option) {
            case 'setBlockEmailSending':
                $this->addFlash('success', 'Status mis à jour !');
                $setting->setBlockEmailSending($value);
                break;
            default:
                $this->addFlash('danger', 'Méthode inconnue dans le switch !');
        }

        $this->em->persist($setting);
        $this->em->flush();

        return $this->redirect($request->headers->get('referer'));

    }

    #[Route('/admin/verification-achats-helloAsso', name: 'admin_verification_achats_helloAsso')]
    public function verificationAchatsHelloAsso(Request $request)
    {

        $bearer = $this->paiementService->helloAssoAuth();

        //on met a jour les paiements
        $payments = $this->paymentRepository->findBy(['timeOfTransaction' => NULL, 'details' => NULL]);

        foreach($payments as $payment){

            $result = $this->paiementService->getHelloAssoPaiementStatus($bearer,$payment);

            $this->paiementService->updateDocumentsWithHelloAssoStatus($result, $payment);

        }

        return new Response('TERMINER: tous les paiements sur HelloAsso ont été mis à jour ! (100% de réussite)');

    }




    // #[Route('/admin/creation-facture-manuelle/', name: 'admin_manual_invoice_billing_address')]
    // public function manualInvoiceBillingAddress(Request $request): Response
    // {
    //     $session = $request->getSession();

    //     if(is_null($session->get('step_manual_invoice'))){

    //         $this->addFlash('warning', 'Réservation inconnue !');
    //         return $this->redirectToRoute('admin');

    //     }else{

    //         $reserve = $this->reserveRepository->find($session->get('step_manual_invoice'));

    //         if(!$reserve){

    //             $this->addFlash('warning', 'Réservation inconnue !');
    //             return $this->redirectToRoute('admin');
    
    //         }else{

    //             $form = $this->createForm(AddressType::class);
    //             $form->handleRequest($request);

    //             return $this->render('admin/manual_invoice/address.html.twig', [
    //                 'reserve' => $reserve,
    //                 'form' => $form
    //             ]);
    //         }

    //     }
    // }
}

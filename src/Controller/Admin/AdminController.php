<?php

namespace App\Controller\Admin;

use App\Entity\SiteSetting;
use App\Repository\UserRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentStatusRepository;
use App\Repository\LegalInformationRepository;
use App\Repository\PaymentRepository;
use App\Repository\SiteSettingRepository;
use App\Service\DocumentService;
use App\Service\MailService;
use App\Service\PaiementService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('v3', name: '')]
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

            $this->documentService->statusChange($document,$status);

            $this->addFlash('success', 'Status mis à jour !');
            return $this->redirectToRoute('admin_traited_daily_commands');

        }
    }

    #[Route('/admin/details-payment/{token}', name: 'admin_invoice_details')]
    public function paymentDetails($token)
    {

        $payment = $this->paymentRepository->findOneBy(['tokenPayment' => $token]);

        $this->paiementService->payplugAuth();

        $result = \Payplug\Payment::retrieve($token);

        return $this->render('admin/details_payment.html.twig', [
            'payment' => $payment,
            'result' => $result
        ]);
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
}

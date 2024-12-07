<?php

namespace App\Controller\Admin;

use App\Entity\Panier;
use DateTimeImmutable;
use PHPUnit\Util\Json;
use App\Entity\Delivery;
use App\Form\AddressType;
use App\Service\MailService;
use App\Service\PanierService;
use App\Form\ManualInvoiceType;
use App\Service\DocumentService;
use App\Service\PaiementService;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use App\Entity\OffSiteOccasionSale;
use App\Repository\BoiteRepository;
use App\Repository\PaymentRepository;
use App\Repository\ReserveRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SiteSettingRepository;
use App\Form\BillingAndDeliveryAddressType;
use App\Repository\DocumentStatusRepository;
use App\Repository\ShippingMethodRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LegalInformationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\BillingAndDeliveryAddressForManualInvoiceType;
use App\Form\DetailsForManualInvoiceType;
use App\Service\UtilitiesService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        private ReserveRepository $reserveRepository,
        private BoiteRepository $boiteRepository,
        private ShippingMethodRepository $shippingMethodRepository,
        private PanierService $panierService,
        private TaxRepository $taxRepository,
        private UtilitiesService $utilitiesService,
        private AdminUrlGenerator $adminUrlGenerator
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

        //on cherche tous les documents crées depuis le 10-11-2024
        $datetimeImmutable = new DateTimeImmutable();
        $date = $datetimeImmutable->setDate(2024, 11, 10);

        $documents = $this->documentRepository->findDocumentsCreatedAfterDateAndNotBilled($date);
        
        foreach($documents as $document){

            $this->paiementService->updateDocumentAndPaiementWithHelloAssoStatus($document);

        }

        return new Response('TERMINER: tous les paiements sur HelloAsso ont été mis à jour ! (100% de réussite)');

    }


    #[Route('/admin/creation-facture-manuelle/details/{reserveId}', name: 'admin_manual_invoice_details')]
    public function manualInvoiceDetails(Request $request, $reserveId): Response
    {
        $reserve = $this->reserveRepository->findOneById($reserveId);

        if(!$reserve){

            $this->addFlash('warning', 'Réservation inconnue !');
            return $this->redirectToRoute('admin');

        }else{

            $detailsForManualInvoiceForm = $this->createForm(DetailsForManualInvoiceType::class);
            $detailsForManualInvoiceForm->handleRequest($request);

            if($detailsForManualInvoiceForm->isSubmitted() && $detailsForManualInvoiceForm->isValid()){

                $details = [
                    'paiementId' => $detailsForManualInvoiceForm['paiement']->getData()->getId(),
                    'shippingMethodId' => $detailsForManualInvoiceForm['shippingMethod']->getData()->getId(),
                    'reserveId' => $reserve->getId()
                ];

                //?on met en session pour passer sur la page suivante
                $request->getSession()->set('detailsForManualInvoice', $details);

                //?on redirige vers la page suivante
                $url = $this->adminUrlGenerator->setRoute('admin_manual_invoice_prices_address', ['reserveId' => $reserve->getId()])->generateUrl();
                return $this->redirect($url);

            }

            return $this->render('admin/manual_invoice/setDetails.html.twig', [
                'reserve' => $reserve,
                'detailsForManualInvoice' => $detailsForManualInvoiceForm,
            ]);
        }
    }

    #[Route('/admin/creation-facture-manuelle/prix-adresses/{reserveId}', name: 'admin_manual_invoice_prices_address')]
    public function manualInvoicePricesAndAddress(Request $request, $reserveId): Response
    {
        $reserve = $this->reserveRepository->findOneById($reserveId);
        $detailsForManualInvoice = $request->getSession()->get('detailsForManualInvoice');


        if(!$reserve or !$detailsForManualInvoice){

            $this->addFlash('warning', 'Réservation ou détails inconnue !');
            return $this->redirectToRoute('admin');

        }else{

            $shippingMethod = $this->shippingMethodRepository->findOneById($detailsForManualInvoice['shippingMethodId']);
            $billingAndDeliveryForm = $this->createForm(BillingAndDeliveryAddressForManualInvoiceType::class, null,[
                'user' => $reserve->getUser(),
                'shippingMethodId' => $shippingMethod->getId(),
            ]);
            $billingAndDeliveryForm->handleRequest($request);

            //on genere des paniers pour chaque occasions
            $paniers = [];
            foreach($reserve->getOccasions() as $occasion){
                $panier = new Panier();
                $panier->setOccasion($occasion);
                $panier->setQte(1);
                $panier->setPriceWithoutTax($occasion->getPriceWithoutTax() * 100);
                $panier->setUnitPriceExclusingTax($occasion->getPriceWithoutTax() * 100);
                $panier->setUser($reserve->getUser());
                $panier->setCreatedAt(new DateTimeImmutable('now'));
                $this->em->persist($panier);
                $paniers[] = $panier;
            }
            //on calcule le poidt total du panier
            $reponses['totauxOccasions'] = $this->utilitiesService->totauxByPanierGroup($paniers);

            if($billingAndDeliveryForm->isSubmitted() && $billingAndDeliveryForm->isValid()){

                $allValuesInRequest = $request->request->all();
                $allPricesHtFromRequest = $allValuesInRequest['billingPricesHt'];
                $billingAddressFromForm = $billingAndDeliveryForm['billingAddress']->getData();
                $deliveryAddressFromForm = $billingAndDeliveryForm['deliveryAddress']->getData();
                $shippingMethod = $this->shippingMethodRepository->find($detailsForManualInvoice['shippingMethodId']);

                $this->documentService->generateDocumentInDatabaseFromReserve($reserve, $allPricesHtFromRequest, $billingAddressFromForm, $deliveryAddressFromForm, $shippingMethod);

                dd('stop document créer');

            }

            return $this->render('admin/manual_invoice/setPricesAndAddress.html.twig', [
                'reserve' => $reserve,
                'billingAndDeliveryForm' => $billingAndDeliveryForm,
                'shippingMethod' => $shippingMethod,
                'shippingMethodIdForJavascript' => $shippingMethod->getId(),
                'cartWeightForJavascript' => $reponses['totauxOccasions']['weigth']
            ]);
        }
    }
}

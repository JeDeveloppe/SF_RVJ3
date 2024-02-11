<?php

namespace App\Controller\Admin;

use DateInterval;
use App\Entity\Tax;
use App\Entity\City;
use App\Entity\Item;
use App\Entity\User;
use App\Entity\Boite;
use App\Entity\Color;
use App\Entity\Level;
use App\Entity\Editor;
use DateTimeImmutable;
use App\Entity\Address;
use App\Entity\Ambassador;
use App\Entity\BadgeForMediaTimeline;
use App\Entity\Country;
use App\Entity\Partner;
use App\Entity\Payment;
use App\Entity\Delivery;
use App\Entity\Discount;
use App\Entity\Document;
use App\Entity\Envelope;
use App\Entity\Occasion;
use App\Entity\ItemGroup;
use App\Entity\Department;
use App\Entity\DocumentLine;
use App\Service\MailService;
use App\Entity\ResetPassword;
use App\Entity\DocumentStatus;
use App\Entity\ShippingMethod;
use App\Entity\CollectionPoint;
use App\Entity\MeansOfPayement;
use App\Entity\LegalInformation;
use App\Entity\MovementOccasion;
use App\Entity\NumbersOfPlayers;
use App\Service\DocumentService;
use App\Entity\ConditionOccasion;
use App\Entity\DocumentLineTotals;
use App\Entity\DocumentParametre;
use App\Entity\Documentsending;
use App\Entity\Media;
use App\Repository\ItemRepository;
use App\Entity\OffSiteOccasionSale;
use App\Entity\Returndetailstostock;
use App\Entity\SiteSetting;
use App\Entity\VoucherDiscount;
use App\Repository\PaymentRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResetPasswordRepository;
use App\Repository\DocumentStatusRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OffSiteOccasionSaleRepository;
use App\Repository\SiteSettingRepository;
use App\Repository\UserRepository;
use App\Service\PaiementService;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;


class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private OffSiteOccasionSaleRepository $offSiteOccasionSaleRepository,
        private PaymentRepository $paymentRepository,
        private DocumentRepository $documentRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private ItemRepository $itemRepository,
        private DocumentService $documentService,
        private ResetPasswordRepository $resetPasswordRepository,
        private SiteSettingRepository $siteSettingRepository,
        private MailService $mailService,
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private PaiementService $paiementService
    )
    {
        
    }
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $now = new DateTimeImmutable('now');

        $documentsToReminder = $this->documentRepository->findByDevisToReminder($now);
        
        $this->mailService->reminderQuotes($documentsToReminder, $now);

        $documentsToDelete = $this->documentRepository->findByDevisToDelete($now);

        $this->documentService->deleteDocumentFromDataBaseAndPuttingItemsBoiteOccasionBackInStock($documentsToDelete);

        $itemsWithStockIsNull = $this->itemRepository->findByStockForSaleIsNull();

        $payments = $this->paymentRepository->findAll();
        //? on recupere dans un tableau les occasions vendu avant la création de document
        $occasionsSales = $this->offSiteOccasionSaleRepository->findBy(['placeOfTransaction' => NULL]);

        $devisEnAttenteDePaiement = $this->documentRepository->findBy(['billNumber' => NULL]);
        $clients = $this->userRepository->findAll();

        $totalPayment = 0;
        foreach($payments as $payment){
            $document = $payment->getDocument();

            if($document){
                $totalPayment += $document->getTotalExcludingTax() ?? 0;
            }
        }

        $totalOccasionSale = 0;
        foreach($occasionsSales as $occasionsSale){
            $totalOccasionSale += $occasionsSale->getMovementPrice();
        }

        $totals[] = [
            'name' => 'CA - Ventes sur le site',
            'total' => $totalPayment,
            'isMoney' => true
        ];
        $totals[] = [
            'name' => 'CA - Ventes hors site (occasions)',
            'total' => $totalOccasionSale,
            'isMoney' => true
            
        ];
        $totals[] = [
            'name' => 'Devis en attente de paiement',
            'total' => count($devisEnAttenteDePaiement),
            'isMoney' => false
        ];
        $totals[] = [
            'name' => 'Nombre d\'inscrits sur le site:',
            'total' => count($clients),
            'isMoney' => false
        ];

        return $this->render('admin/dashboard.html.twig', [
            'totals' => $totals,
            'itemsWithStockIsNull' => $itemsWithStockIsNull
        ]);
    }

    #[Route('/admin/traitement-quotidien/commandes', name: 'admin_traited_daily_commands')]
    public function commandesTraitedDaily(): Response
    {
        $datas = [];
        $status = [];
        $statusToBeTraitedDailys = $this->documentStatusRepository->findStatusIsTraitedDaily();
        $actions = $this->documentStatusRepository->findAll();
        $setting = $this->siteSettingRepository->findOneBy([]);

        foreach($actions as $action){
            $status[$action->getAction()] = $action->getAction();
        }


        foreach($statusToBeTraitedDailys as $statusToBeTraitedDaily){

            $datas[$statusToBeTraitedDaily->getAction()] = 
                [
                    'value' => $statusToBeTraitedDaily->getName(),
                    'action' => $statusToBeTraitedDaily->getAction(),
                    'documents' => $this->documentRepository->findDocumentsToBeTraitedDailyWithStatus($statusToBeTraitedDaily)
                ];
        }
        

        return $this->render('admin/traited_daily_commands.html.twig', [
            'datas' => $datas,
            'status' => $status,
            'setting' => $setting
        ]);
    }

    #[Route('/admin/traitement-quotidien/devis', name: 'admin_traited_daily_devis')]
    public function devisTraitedDaily(): Response
    {
        $entityDevisWithoutPrice = $this->documentStatusRepository->findOneBy(['action' => 'DEVIS_WITHOUT_PRICE']);
        $entityDevisWithPrice = $this->documentStatusRepository->findOneBy(['action' => 'NO_PAID']);

        $devisWithoutPrice = $this->documentRepository->findBy(['documentStatus' => $entityDevisWithoutPrice]);
        $devisWithPrice = $this->documentRepository->findBy(['documentStatus' => $entityDevisWithPrice]);

        return $this->render('admin/traited_daily_devis.html.twig', [
            'devisWithPrice' => $devisWithPrice,
            'devisWithoutPrice' => $devisWithoutPrice
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('RVJ3');
    }

    public function configureMenuItems(): iterable
    {
        $resetPasswords = $this->resetPasswordRepository->findBy(['isUsed' => false]);

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');        
        yield MenuItem::linkToRoute('SITE','fa-solid fa-shop','app_home');

        yield MenuItem::section('Traitements quotidien:');
        yield MenuItem::linkToCrud('RETOUR EN STOCK','fa-solid fa-rotate-left', Returndetailstostock::class);
        yield MenuItem::linkToRoute('DEVIS','fa-solid fa-money-bill','admin_traited_daily_devis');
        yield MenuItem::linkToRoute('COMMANDES','fa-solid fa-money-bill','admin_traited_daily_commands');
        yield MenuItem::linkToRoute('GRAPHIQUES','fa-solid fa-chart-simple','jpgraph');

        

        yield MenuItem::section('Gestion des boites:');
        yield MenuItem::linkToCrud('Boites', 'fas fa-list', Boite::class);
        yield MenuItem::linkToCrud('Éditeurs', 'fas fa-list', Editor::class);
        yield MenuItem::linkToCrud('Joueurs', 'fas fa-list', NumbersOfPlayers::class);
        
        yield MenuItem::section('Gestion des articles:');
        yield MenuItem::linkToCrud('Groupe d\'articles', 'fas fa-list', ItemGroup::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-list', Item::class);
        yield MenuItem::linkToCrud('Couleurs', 'fas fa-list', Color::class);
        yield MenuItem::linkToCrud('Enveloppes', 'fas fa-list', Envelope::class);

        yield MenuItem::section('Gestion des documents:');
        yield MenuItem::linkToCrud('Documents', 'fas fa-list', Document::class);
        // yield MenuItem::linkToCrud('Lignes documents', 'fas fa-list', DocumentLine::class);
        yield MenuItem::linkToCrud('Paiements', 'fas fa-list', Payment::class);
        yield MenuItem::linkToCrud('Status des documents', 'fas fa-list', DocumentStatus::class);
        yield MenuItem::linkToCrud('Paramètres', 'fas fa-list', DocumentParametre::class);
        yield MenuItem::linkToCrud('Liste des envois', 'fas fa-list', Documentsending::class);
        yield MenuItem::linkToCrud('Liste des totaux', 'fas fa-list', DocumentLineTotals::class);

        yield MenuItem::section('Gestion des utilisateurs:');
        yield MenuItem::linkToCrud('Liste des clients', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Liste des levels', 'fas fa-list', Level::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Liste des adresses', 'fas fa-list', Address::class);
        yield MenuItem::linkToCrud('Chgmts de mdp', 'fas fa-list', ResetPassword::class)
            ->setBadge(count($resetPasswords),'info');

        yield MenuItem::section('Gestion des occasions:');
        yield MenuItem::linkToCrud('Liste des occasions', 'fas fa-list', Occasion::class);
        yield MenuItem::linkToCrud('Liste des ventes / dons', 'fas fa-list', OffSiteOccasionSale::class);
        yield MenuItem::linkToCrud('Types de mouvement', 'fas fa-list', MovementOccasion::class);
        yield MenuItem::linkToCrud('Liste des états (pièces, boite, règle)', 'fas fa-list', ConditionOccasion::class);
        
        yield MenuItem::section('Gestion des paniers:');
        yield MenuItem::linkToCrud('Moyens de retrait/envoi', 'fas fa-list', ShippingMethod::class);
        yield MenuItem::linkToCrud('Lieux de retrait', 'fas fa-list', CollectionPoint::class);
        yield MenuItem::linkToCrud('Remises', 'fas fa-list', Discount::class);
        yield MenuItem::linkToCrud('Bon d\'achat', 'fas fa-list', VoucherDiscount::class);
        yield MenuItem::linkToCrud('Prix des livraisons', 'fas fa-list', Delivery::class);

        yield MenuItem::section('Gestion des ambassadeurs');
        yield MenuItem::linkToCrud('Liste des ambassadeurs', 'fas fa-list', Ambassador::class);

        yield MenuItem::section('Gestion des partenaires');
        yield MenuItem::linkToCrud('Liste des partenaires', 'fas fa-list', Partner::class);

        yield MenuItem::section('Gestion des médias');
        yield MenuItem::linkToCrud('Liste des médias', 'fas fa-list', Media::class);
        yield MenuItem::linkToCrud('Liste des badges', 'fas fa-list', BadgeForMediaTimeline::class);

        yield MenuItem::section('Légale:');
        yield MenuItem::linkToCrud('Informations', 'fas fa-list', LegalInformation::class);
        yield MenuItem::linkToCrud('Taxes', 'fas fa-list', Tax::class);

        yield MenuItem::section('Paramètres géographiques:');
        yield MenuItem::linkToCrud('Villes', 'fas fa-list', City::class);
        yield MenuItem::linkToCrud('Departements', 'fas fa-list', Department::class);
        yield MenuItem::linkToCrud('Pays', 'fas fa-list', Country::class);

        yield MenuItem::section('Paramètres de ventes:');
        yield MenuItem::linkToCrud('Moyens de paiement', 'fas fa-list', MeansOfPayement::class);

        yield MenuItem::section('Paramètres du site:');
        yield MenuItem::linkToCrud('Paramètres', 'fas fa-gear', SiteSetting::class);

    }
}

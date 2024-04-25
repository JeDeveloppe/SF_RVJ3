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
use App\Entity\Media;
use App\Entity\Editor;
use DateTimeImmutable;
use App\Entity\Address;
use App\Entity\Country;
use App\Entity\Partner;
use App\Entity\Payment;
use App\Entity\Delivery;
use App\Entity\Discount;
use App\Entity\Document;
use App\Entity\Envelope;
use App\Entity\Occasion;
use App\Entity\ItemGroup;
use App\Entity\Ambassador;
use App\Entity\Department;
use App\Entity\SiteSetting;
use App\Entity\DocumentLine;
use App\Service\MailService;
use App\Entity\ResetPassword;
use App\Entity\DocumentStatus;
use App\Entity\ShippingMethod;
use App\Entity\CollectionPoint;
use App\Entity\Documentsending;
use App\Entity\MeansOfPayement;
use App\Entity\VoucherDiscount;
use App\Entity\LegalInformation;
use App\Entity\MovementOccasion;
use App\Entity\NumbersOfPlayers;
use App\Service\DocumentService;
use App\Service\PaiementService;
use App\Entity\ConditionOccasion;
use App\Entity\DocumentParametre;
use App\Entity\DocumentLineTotals;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use App\Entity\OffSiteOccasionSale;
use App\Entity\Returndetailstostock;
use App\Entity\BadgeForMediaTimeline;
use App\Entity\Reserve;
use App\Repository\PaymentRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SiteSettingRepository;
use App\Repository\ResetPasswordRepository;
use App\Repository\DocumentStatusRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OffSiteOccasionSaleRepository;
use App\Repository\PanierRepository;
use App\Repository\ReserveRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
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
        private PaiementService $paiementService,
        private ReserveRepository $reserveRepository,
        private PanierRepository $panierRepository
    )
    {
        
    }
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $now = new DateTimeImmutable('now');

        $documentsToReminder = $this->documentRepository->findByDevisToReminder($now);
        $setting = $this->siteSettingRepository->findOneBy([]);
        
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
            'itemsWithStockIsNull' => $itemsWithStockIsNull,
            'setting' => $setting,
            'paniersOccasionsInCarts' => $this->panierRepository->findAllOccasionsInCart()
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
        $statusToBeTraitedDailys = $this->documentStatusRepository->findStatusIsTraitedDaily();

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');        
        yield MenuItem::linkToRoute('SITE','fa-solid fa-shop','app_home');


        yield MenuItem::section('Traitements quotidien:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('RETOUR EN STOCK','fa-solid fa-rotate-left', Returndetailstostock::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToRoute('DEVIS','fa-solid fa-money-bill','admin_traited_daily_devis')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToRoute('COMMANDES','fa-solid fa-money-bill','admin_traited_daily_commands')->setPermission('ROLE_ADMIN')
            ->setBadge(count($this->documentRepository->findDocumentsToBeTraitedDailyWithStatus($statusToBeTraitedDailys[0])),'success');
        yield MenuItem::linkToRoute('GRAPHIQUES','fa-solid fa-chart-simple','jpgraph')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('RESERVER DES OCCASIONS','fa-solid fa-hand', Reserve::class)->setPermission('ROLE_ADMIN')
            ->setBadge(count($this->reserveRepository->findAll()),'warning');

        yield MenuItem::section('Gestion des boites:')->setPermission('ROLE_BENEVOLE');
        yield MenuItem::linkToCrud('Boites', 'fas fa-list', Boite::class)->setPermission('ROLE_BENEVOLE');
        yield MenuItem::linkToCrud('Éditeurs', 'fas fa-list', Editor::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Joueurs', 'fas fa-list', NumbersOfPlayers::class)->setPermission('ROLE_ADMIN');
        
        yield MenuItem::section('Gestion des articles:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Groupe d\'articles', 'fas fa-list', ItemGroup::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Articles', 'fas fa-list', Item::class)->setPermission('ROLE_ADMIN');
        // yield MenuItem::linkToCrud('Couleurs', 'fas fa-list', Color::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Enveloppes', 'fas fa-list', Envelope::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Gestion des documents:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des documents', 'fas fa-list', Document::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        // yield MenuItem::linkToCrud('Lignes documents', 'fas fa-list', DocumentLine::class);
        yield MenuItem::linkToCrud('Liste des paiements', 'fas fa-list', Payment::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Status des documents', 'fas fa-list', DocumentStatus::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Paramètres', 'fas fa-list', DocumentParametre::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        // yield MenuItem::linkToCrud('Liste des totaux', 'fas fa-list', DocumentLineTotals::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Gestion des utilisateurs:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des clients', 'fas fa-list', User::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des adresses', 'fas fa-list', Address::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des roles', 'fas fa-list', Level::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Chgmts de mdp', 'fas fa-list', ResetPassword::class)
            ->setBadge(count($resetPasswords),'info')->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Gestion des occasions:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des occasions', 'fas fa-list', Occasion::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des ventes / dons', 'fas fa-list', OffSiteOccasionSale::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Types de mouvement', 'fas fa-list', MovementOccasion::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des états (pièces, boite, règle)', 'fas fa-list', ConditionOccasion::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        
        yield MenuItem::section('Gestion des paniers:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Moyens de retrait/envoi', 'fas fa-list', ShippingMethod::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Lieux de retrait', 'fas fa-list', CollectionPoint::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Remises', 'fas fa-list', Discount::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Bon d\'achat', 'fas fa-list', VoucherDiscount::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Prix des livraisons', 'fas fa-list', Delivery::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Gestion des ambassadeurs')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des ambassadeurs', 'fas fa-list', Ambassador::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Gestion des partenaires')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des partenaires', 'fas fa-list', Partner::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Gestion des médias')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des médias', 'fas fa-list', Media::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Liste des badges', 'fas fa-list', BadgeForMediaTimeline::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Légale:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Informations', 'fas fa-list', LegalInformation::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Taxes', 'fas fa-list', Tax::class)->setPermission('ROLE_ADMIN')->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Paramètres géographiques:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Villes', 'fas fa-list', City::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Departements', 'fas fa-list', Department::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Pays', 'fas fa-list', Country::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Paramètres de ventes:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Moyens de paiement', 'fas fa-list', MeansOfPayement::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Paramètres du site:')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Paramètres', 'fas fa-gear', SiteSetting::class)->setPermission('ROLE_ADMIN');

    }
}

<?php

namespace App\Controller\Admin;

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
use App\Entity\DocumentParametre;
use App\Repository\ItemRepository;
use App\Entity\OffSiteOccasionSale;
use App\Entity\Returndetailstostock;
use App\Repository\PaymentRepository;
use App\Repository\DocumentRepository;
use App\Repository\ResetPasswordRepository;
use App\Repository\DocumentStatusRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OffSiteOccasionSaleRepository;
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
        private ResetPasswordRepository $resetPasswordRepository
    )
    {
        
    }
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $now = new DateTimeImmutable('now');

        $documentsToDelete = $this->documentRepository->findByDevisToDelete($now);

        $this->documentService->deleteDocumentFromDataBaseAndPuttingItemsBoiteOccasionBackInStock($documentsToDelete);

        $itemsWithStockIsNull = $this->itemRepository->findByStockForSaleIsNull();

        $payments = $this->paymentRepository->findAll();
        $occasionsSales = $this->offSiteOccasionSaleRepository->findAll();

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
            'total' => $totalPayment
        ];
        $totals[] = [
            'name' => 'CA - Ventes hors site (occasions)',
            'total' => $totalOccasionSale
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
            'status' => $status
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

        yield MenuItem::section('Gestion des boites:');
        yield MenuItem::linkToCrud('Boites', 'fas fa-list', Boite::class);
        yield MenuItem::linkToCrud('Éditeurs', 'fas fa-list', Editor::class);
        yield MenuItem::linkToCrud('Joueurs', 'fas fa-list', NumbersOfPlayers::class);
        
        yield MenuItem::section('Gestion des partenaires');
        yield MenuItem::linkToCrud('Liste des partenaires', 'fas fa-list', Partner::class);
        
        yield MenuItem::section('Gestion des articles:');
        yield MenuItem::linkToCrud('Groupe d\'articles', 'fas fa-list', ItemGroup::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-list', Item::class);
        yield MenuItem::linkToCrud('Couleurs', 'fas fa-list', Color::class);
        yield MenuItem::linkToCrud('Enveloppes', 'fas fa-list', Envelope::class);

        yield MenuItem::section('Gestion des utilisateurs:');
        yield MenuItem::linkToCrud('Liste des clients', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Liste des levels', 'fas fa-list', Level::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Liste des adresses', 'fas fa-list', Address::class);
        yield MenuItem::linkToCrud('Chgmts de mdp', 'fas fa-list', ResetPassword::class)
            ->setBadge(count($resetPasswords),'info');
        
        yield MenuItem::section('Gestion des paniers:');
        yield MenuItem::linkToCrud('Moyens de retrait/envoi', 'fas fa-list', ShippingMethod::class);
        yield MenuItem::linkToCrud('Lieux de retrait', 'fas fa-list', CollectionPoint::class);

        yield MenuItem::section('Gestion des documents:');
        yield MenuItem::linkToCrud('Documents', 'fas fa-list', Document::class);
        yield MenuItem::linkToCrud('Lignes documents', 'fas fa-list', DocumentLine::class);
        yield MenuItem::linkToCrud('Paiements', 'fas fa-list', Payment::class);
        yield MenuItem::linkToCrud('Status des documents', 'fas fa-list', DocumentStatus::class);
        yield MenuItem::linkToCrud('Paramètres', 'fas fa-list', DocumentParametre::class);

        yield MenuItem::section('Gestion des occasions:');
        yield MenuItem::linkToCrud('Liste des occasions', 'fas fa-list', Occasion::class);
        yield MenuItem::linkToCrud('Liste des ventes / dons', 'fas fa-list', OffSiteOccasionSale::class);
        yield MenuItem::linkToCrud('Types de mouvement', 'fas fa-list', MovementOccasion::class);
        yield MenuItem::linkToCrud('Liste des états (pièces, boite, règle)', 'fas fa-list', ConditionOccasion::class);
        
        yield MenuItem::section('Légale:');
        yield MenuItem::linkToCrud('Informations', 'fas fa-list', LegalInformation::class);
        yield MenuItem::linkToCrud('Taxes', 'fas fa-list', Tax::class);

        yield MenuItem::section('Paramètres géographiques:');
        yield MenuItem::linkToCrud('Villes', 'fas fa-list', City::class);
        yield MenuItem::linkToCrud('Departements', 'fas fa-list', Department::class);
        yield MenuItem::linkToCrud('Pays', 'fas fa-list', Country::class);

        yield MenuItem::section('Paramètres de ventes:');
        yield MenuItem::linkToCrud('Moyens de paiement', 'fas fa-list', MeansOfPayement::class);

        yield MenuItem::section('Paramètres du panier:');
        yield MenuItem::linkToCrud('Remises', 'fas fa-list', Discount::class);
        yield MenuItem::linkToCrud('Prix des livraisons', 'fas fa-list', Delivery::class);


    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Entity\Boite;
use App\Entity\City;
use App\Entity\CollectionPoint;
use App\Entity\Color;
use App\Entity\ConditionOccasion;
use App\Entity\Country;
use App\Entity\Delivery;
use App\Entity\Department;
use App\Entity\Discount;
use App\Entity\Document;
use App\Entity\DocumentLine;
use App\Entity\DocumentStatus;
use App\Entity\Editor;
use App\Entity\Envelope;
use App\Entity\LegalInformation;
use App\Entity\MeansOfPayement;
use App\Entity\MovementOccasion;
use App\Entity\NumbersOfPlayers;
use App\Entity\Occasion;
use App\Entity\OffSiteOccasionSale;
use App\Entity\Partner;
use App\Entity\Payment;
use App\Entity\ShippingMethod;
use App\Entity\Tax;
use App\Entity\User;
use App\Repository\DocumentRepository;
use App\Repository\DocumentStatusRepository;
use App\Repository\OffSiteOccasionSaleRepository;
use App\Repository\PaymentRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private OffSiteOccasionSaleRepository $offSiteOccasionSaleRepository,
        private PaymentRepository $paymentRepository,
        private DocumentRepository $documentRepository,
        private DocumentStatusRepository $documentStatusRepository
    )
    {
        
    }
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
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
            'totals' => $totals
        ]);
    }

    #[Route('/admin/traitement-quotidien', name: 'admin_traited_daily')]
    public function traitedDaily(): Response
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
        

        return $this->render('admin/traited_daily.html.twig', [
            'datas' => $datas,
            'status' => $status
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('RVJ3');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('SITE','fa-solid fa-shop','app_home');
        yield MenuItem::linkToRoute('COMMANDES','fa-solid fa-money-bill','admin_traited_daily');

        yield MenuItem::section('Gestion des boites:');
        yield MenuItem::linkToCrud('Boites', 'fas fa-list', Boite::class);
        yield MenuItem::linkToCrud('Éditeurs', 'fas fa-list', Editor::class);
        yield MenuItem::linkToCrud('Joueurs', 'fas fa-list', NumbersOfPlayers::class);
        
        yield MenuItem::section('Listes');
        yield MenuItem::linkToCrud('Partenaires', 'fas fa-list', Partner::class);
        
        yield MenuItem::section('Gestion des articles:');
        yield MenuItem::linkToCrud('Couleurs', 'fas fa-list', Color::class);
        yield MenuItem::linkToCrud('Enveloppes', 'fas fa-list', Envelope::class);

        yield MenuItem::section('Gestion des utilisateurs:');
        yield MenuItem::linkToCrud('Clients', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Adresses', 'fas fa-list', Address::class);
        
        yield MenuItem::section('Gestion des paniers:');
        yield MenuItem::linkToCrud('Moyens de retrait/envoi', 'fas fa-list', ShippingMethod::class);
        yield MenuItem::linkToCrud('Lieux de retrait', 'fas fa-list', CollectionPoint::class);

        yield MenuItem::section('Gestion des documents:');
        yield MenuItem::linkToCrud('Documents', 'fas fa-list', Document::class);
        yield MenuItem::linkToCrud('Lignes documents', 'fas fa-list', DocumentLine::class);
        yield MenuItem::linkToCrud('Paiements', 'fas fa-list', Payment::class);
        yield MenuItem::linkToCrud('Status des documents', 'fas fa-list', DocumentStatus::class);

        yield MenuItem::section('Gestion des occasions:');
        yield MenuItem::linkToCrud('Occasions', 'fas fa-list', Occasion::class);
        yield MenuItem::linkToCrud('Ventes / dons', 'fas fa-list', OffSiteOccasionSale::class);
        yield MenuItem::linkToCrud('Mouvements des occasions', 'fas fa-list', MovementOccasion::class);
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

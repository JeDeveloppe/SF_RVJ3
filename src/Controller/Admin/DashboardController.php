<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Entity\Boite;
use App\Entity\City;
use App\Entity\ConditionOccasion;
use App\Entity\Country;
use App\Entity\Department;
use App\Entity\Editor;
use App\Entity\LegalInformation;
use App\Entity\MeansOfPayement;
use App\Entity\MovementOccasion;
use App\Entity\Occasion;
use App\Entity\OffSiteOccasionSale;
use App\Entity\Partner;
use App\Entity\ShippingMethod;
use App\Entity\Tax;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
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
        return $this->render('admin/dashboard.html.twig');
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

        yield MenuItem::section('Catalogues');
        yield MenuItem::linkToCrud('Boites', 'fas fa-list', Boite::class);
        yield MenuItem::linkToCrud('Occasions', 'fas fa-list', Occasion::class);
        yield MenuItem::linkToCrud('Partenaires', 'fas fa-list', Partner::class);
        yield MenuItem::linkToCrud('Éditeurs', 'fas fa-list', Editor::class);

        yield MenuItem::section('Gestion des utilisateurs:');
        yield MenuItem::linkToCrud('Clients', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Adresses', 'fas fa-list', Address::class);

        yield MenuItem::section('Paramètres géographiques:');
        yield MenuItem::linkToCrud('Villes', 'fas fa-list', City::class);
        yield MenuItem::linkToCrud('Departements', 'fas fa-list', Department::class);
        yield MenuItem::linkToCrud('Pays', 'fas fa-list', Country::class);

        yield MenuItem::section('Gestion des occasions:');
        yield MenuItem::linkToCrud('Ventes / dons', 'fas fa-list', OffSiteOccasionSale::class);
        yield MenuItem::linkToCrud('Liste des états (pièces, boite, règle)', 'fas fa-list', ConditionOccasion::class);
        
        yield MenuItem::section('Légale:');
        yield MenuItem::linkToCrud('Informations', 'fas fa-list', LegalInformation::class);
        yield MenuItem::linkToCrud('Taxes', 'fas fa-list', Tax::class);

        yield MenuItem::section('Paramètres:');
        yield MenuItem::linkToCrud('Mouvements de paiement', 'fas fa-list', MovementOccasion::class);
        yield MenuItem::linkToCrud('Moyens de retrait', 'fas fa-list', ShippingMethod::class);
        yield MenuItem::linkToCrud('Moyens de paiement', 'fas fa-list', MeansOfPayement::class);

    }
}

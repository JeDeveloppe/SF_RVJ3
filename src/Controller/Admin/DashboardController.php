<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Entity\Boite;
use App\Entity\City;
use App\Entity\Country;
use App\Entity\Department;
use App\Entity\Editor;
use App\Entity\Partner;
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
        yield MenuItem::linkToCrud('Villes', 'fas fa-list', City::class);
        yield MenuItem::linkToCrud('Departements', 'fas fa-list', Department::class);
        yield MenuItem::linkToCrud('Pays', 'fas fa-list', Country::class);
        yield MenuItem::linkToCrud('Clients', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Adresses', 'fas fa-list', Address::class);
        yield MenuItem::linkToCrud('Boites', 'fas fa-list', Boite::class);
        yield MenuItem::linkToCrud('Éditeurs', 'fas fa-list', Editor::class);
        yield MenuItem::linkToCrud('Partenaires', 'fas fa-list', Partner::class);

    }
}

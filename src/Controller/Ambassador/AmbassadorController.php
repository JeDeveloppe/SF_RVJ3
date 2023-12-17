<?php

namespace App\Controller\Ambassador;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AmbassadorController extends AbstractController
{
    #[Route('/devenir-ambassadeur', name: 'app_became_ambassador')]
    public function becameAmbassador(): Response
    {
        return $this->render('site/ambassador/devenir_ambassador.html.twig', [
            'controller_name' => 'AmbassadorController',
        ]);
    }

    #[Route('/ambassador', name: 'ambassador_home')]
    public function index(): Response
    {
        return $this->render('site/ambassador/ambassador.html.twig', [
            'controller_name' => 'AmbassadorController',
        ]);
    }
}

<?php

namespace App\Controller\Site;

use App\Repository\LegalInformationRepository;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    public function __construct(
        private LegalInformationRepository $legalInformationRepository
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function mentionsLegales(): Response
    {
        $legales = $this->legalInformationRepository->findOneBy(['isOnline' => true], ['id' => 'ASC']);

        return $this->render('site/legale/mentions_legales.html.twig', [
            'legales' => $legales,
        ]);
    }

    #[Route('/conditions-generale-de-vente', name: 'app_conditions_generale_de_vente')]
    public function cgv(): Response
    {
        $legales = $this->legalInformationRepository->findOneBy(['isOnline' => true], ['id' => 'ASC']);

        return $this->render('site/legale/cgv.html.twig', [
            'legales' => $legales,
        ]);
    }

    #[Route('/nos-partenaires', name: 'app_partenaires')]
    public function partenaires(PartnerRepository $partnerRepository): Response
    {
        $partenaires = $partnerRepository->findBy(['isOnline' => true], ['name' => 'ASC']);

        return $this->render('site/partner/partners.html.twig', [
            'partners' => $partenaires,
        ]);
    }
}

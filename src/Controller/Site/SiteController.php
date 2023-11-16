<?php

namespace App\Controller\Site;

use App\Form\ContactType;
use App\Repository\PartnerRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LegalInformationRepository;
use App\Service\PanierService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    public function __construct(
        private LegalInformationRepository $legalInformationRepository,
        private PanierService $panierService
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

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
    
        if($form->isSubmitted() && $form->isValid()) {
    
            $mailerService->sendEmailContact(
                $informationsLegales->getAdresseMailSite(),
                $form->get('email')->getData(),
                "Message du site concernant: ".$form->get('sujet')->getData(),
                [
                    'expediteur' => $form->get('email')->getData(),
                    'message' => $form->get('message')->getData()
                ]
            );
    
            $this->addFlash('success', 'Message bien envoyé!');
            return $this->redirectToRoute('contact');
        }
    
        return $this->render('site/contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }





   
}

<?php

namespace App\Controller\Site;

use App\Form\ContactType;
use App\Repository\DocumentLineRepository;
use App\Service\PanierService;
use App\Repository\PartnerRepository;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LegalInformationRepository;
use App\Service\DocumentService;
use App\Service\UtilitiesService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class SiteController extends AbstractController
{
    public function __construct(
        private LegalInformationRepository $legalInformationRepository,
        private PanierService $panierService,
        private DocumentService $documentService,
        private UtilitiesService $utilitiesService
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

    #[Route('/document/{tokenDocument}', name: 'document_view')]
    public function lectureDevis(
        $tokenDocument,
        DocumentRepository $documentRepository,
        DocumentLineRepository $documentLineRepository,
        Security $security
        ): Response
    {

        //on cherche le devis par le token et s'il n'est pas deja annuler par l'utilisateur
        // $devis = $documentRepository->findOneBy(['token' => $token, 'isDeleteByUser' => null, 'numeroFacture' => null]);

        $document = $documentRepository->findOneBy(['token' => $tokenDocument]);

        if(!$document){

            $tableau = [
                'h1' => 'Document non trouvé !',
                'p1' => 'La consultation de ce document est impossible!',
                'p2' => 'Document inconnu ou supprimé !'
            ];

            return $this->render('site/document_view/_end_view.html.twig', [
                'tableau' => $tableau
            ]);

        }else{

            // if($this->securiserService->isGranted($user, 'ROLE_ADMIN')){
            //     $checkRole = false;
            // }
            $docLines = $document->getDocumentLines();
            $tauxTva = $this->utilitiesService->calculTauxTva($document->getTaxRateValue());

            foreach($docLines as $docLine){

                $docLine_items = $documentLineRepository->findBy(['document' => $docLine->getDocument()->getId(), 'occasion' => null, 'boite' => null ]);
                $docLine_occasions = $documentLineRepository->findBy(['document' => $docLine->getDocument()->getId(), 'item' => null, 'boite' => null]);
                $docLine_boites = $documentLineRepository->findBy(['document' => $docLine->getDocument()->getId(), 'occasion' => null, 'item' => null]);

            }

            return $this->render('site/document_view/_document_view.html.twig', [
                'document' => $document,
                'docLine_items' => $docLine_items,
                'docLine_occasions' => $docLine_occasions,
                'docLine_boites' => $docLine_boites,
                'tva' => $tauxTva
            ]);
        }
    }
}
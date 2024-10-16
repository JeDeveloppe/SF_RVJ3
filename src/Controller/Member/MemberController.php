<?php

namespace App\Controller\Member;

use App\Form\UserType;
use App\Service\DocumentService;
use App\Service\UtilitiesService;
use App\Repository\PanierRepository;
use App\Repository\AddressRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LegalInformationRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\DocumentParametreRepository;
use App\Service\MailService;
use DateTimeImmutable;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemberController extends AbstractController
{

    public function __construct(
        private DocumentRepository $documentRepository,
        private Security $security,
        private UtilitiesService $utilitiesService,
        private PaginatorInterface $paginator,
        private PanierRepository $panierRepository,
        private AddressRepository $addressRepository,
        private DocumentService $documentService,
        private LegalInformationRepository $legalInformationRepository,
        private EntityManagerInterface $em,
        private MailService $mailService
        )
    {
    }

    #[Route('/membre', name: 'member')]
    public function member(DocumentParametreRepository $documentParametreRepository, DocumentRepository $documentRepository, Request $request): Response
    {
        $user = $this->security->getUser();

        //relance email des devis
        $now = new DateTimeImmutable('now');
        $this->documentService->reminderQuotes($now);
        //on supprime les document trop vieu non relancer
        $this->documentService->deleteDocumentFromDataBaseAndPuttingItemsBoiteOccasionBackInStock();

        $themes[] = [
            'title' => 'Mes commandes',
            'imgName' => 'commandes',
            'link' => $this->generateUrl('member_historique')
        ];
        $themes[] = [
            'title' => 'Mes adresses',
            'imgName' => 'adresses',
            'link' => $this->generateUrl('member_adresses')           
        ];
        $themes[] = [
            'title' => 'Mes paramètres',
            'imgName' => 'parametres',
            'link' => $this->generateUrl('member_compte')           
        ];

        return $this->render('member/member.html.twig', ['themes' => $themes]);
    }
    
    #[Route('/membre/adresses', name: 'member_adresses')]
    public function membreAdresses(): Response
    {
        $user = $this->security->getUser();
        $nbrOfAdressesMax = $_ENV['NBR_MAX_ADDRESSES_FOR_MEMBER'];


        return $this->render('member/adresse/index.html.twig', [
            'livraison_adresses' => $this->addressRepository->findBy(['user' => $user, 'isFacturation' => false]),
            'facturation_adresses' => $this->addressRepository->findBy(['user' => $user, 'isFacturation' => true]),
            'nbrOfAdressesMax' => $nbrOfAdressesMax
        ]);

    }

    #[Route('/membre/historique', name: 'member_historique')]
    public function memberHistorique(DocumentParametreRepository $documentParametreRepository, DocumentRepository $documentRepository, Request $request): Response
    {
        $user = $this->security->getUser();

        //relance email des devis
        $now = new DateTimeImmutable('now');
        $this->documentService->reminderQuotes($now);
        //on supprime les document trop vieu non relancer
        $this->documentService->deleteDocumentFromDataBaseAndPuttingItemsBoiteOccasionBackInStock();

        $donnees = $documentRepository->findBy(['user' => $user, 'isDeleteByUser' => false], ['id' => 'DESC']);

        $limitPerPage = 10; //TODO Antoine
        $documents = $this->paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $limitPerPage /*limit per page*/
        );

        return $this->render('member/historique.html.twig', [
            'documents' => $documents,
            'docParams' => $documentParametreRepository->findOneBy([]),
            'limitPerPage' => $limitPerPage
        ]);
    }

    #[Route('/membre/mon-compte', name: 'member_compte')]
    public function membreCompte(
        Request $request): Response
    {
        $user = $this->security->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
    
            $this->em->persist($user);
            $this->em->flush();
            
            return $this->redirectToRoute('member_compte', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('member/compte.html.twig', [
            'form' => $form->createView()
            ]);
    }

    #[Route('/membre/delete/document/{tokenDocument}', name: 'member_delete_document')]
    public function deleteDocument($tokenDocument)
    {

        $documentsToDelete = [];

        $document = $this->documentRepository->findOneBy(['token' => $tokenDocument]);
        
        if(!$document){

            return $this->documentService->renderIfDocumentNoExist();

        }else{
            
            $documentsToDelete[] = $document;

            $this->documentService->deleteDocumentFromDataBaseAndPuttingItemsBoiteOccasionBackInStock($documentsToDelete);

            $tableau = [
                'h1' => 'Document supprimé !',
                'p1' => 'La modification de ce document est prise en compte !',
                'p2' => 'Vous ne pouvez plus le consulter !'
            ];

            return $this->render('site/document_view/_end_view.html.twig', [
                'tableau' => $tableau
            ]);
        }

    }

    #[Route('/membre/download/facture/{tokenDocument}', name: 'member_facture_download')]
    public function factureDownload($tokenDocument)
    {
        $user = $this->security->getUser();

        $document = $this->documentRepository->findOneBy(['token' => $tokenDocument, 'user' => $user]);

        if(!$document){

            return $this->documentService->renderIfDocumentNoExist();

        }else{

            $pdf = $this->documentService->generatePdf($document);

            return new Response($pdf->Output(), 200, array(
                'Content-Type' => 'application/pdf'));
        }
    }
}

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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\DocumentParametreRepository;
use App\Repository\LegalInformationRepository;
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
        private EntityManagerInterface $em
        )
    {
    }

    #[Route('/membre/adresses', name: 'member_adresses')]
    public function membreAdresses(): Response
    {
        $user = $this->security->getUser();

        return $this->render('member/adresse/index.html.twig', [
            'livraison_adresses' => $this->addressRepository->findBy(['user' => $user, 'isFacturation' => false]),
            'facturation_adresses' => $this->addressRepository->findBy(['user' => $user, 'isFacturation' => true]),
        ]);

    }

    #[Route('/membre', name: 'member')]
    public function membreHistorique(DocumentParametreRepository $documentParametreRepository, Request $request): Response
    {
        $user = $this->security->getUser();
                
        $donnees = $user->getDocuments();

        $documents = $this->paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('member/historique.html.twig', [
            'documents' => $documents,
            'docParams' => $documentParametreRepository->findOneBy([])
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
    public function factureDownload($tokenDocument, Request $request)
    {
        $user = $this->security->getUser();

        $document = $this->documentRepository->findOneBy(['token' => $tokenDocument, 'user' => $user]);

        if(!$document){

            return $this->documentService->renderIfDocumentNoExist();

        }else{

            $this->documentService->generateFpdf($document, $request);

            return new Response();
        }
    }
}

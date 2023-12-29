<?php

namespace App\Controller\Member;


use Dompdf\Dompdf;
use Dompdf\Options;
use App\Form\UserType;
use DateTimeImmutable;
use App\Service\DocumentService;
use App\Service\UtilitiesService;
use App\Repository\UserRepository;
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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('v3', name: '')]
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

            $tableau = [
                'h1' => 'Document non trouvé !',
                'p1' => 'La modification de ce document est impossible!',
                'p2' => 'Document inconnu ou supprimé !'
            ];

        }else{
            
            $documentsToDelete[] = $document;

            $this->documentService->deleteDocumentFromDataBaseAndPuttingItemsBoiteOccasionBackInStock($documentsToDelete);

            $tableau = [
                'h1' => 'Document supprimé !',
                'p1' => 'La modification de ce document est prise en compte !',
                'p2' => 'Vous ne pouvez plus le consulter !'
            ];

        }

        return $this->render('site/document_view/_end_view.html.twig', [
            'tableau' => $tableau
        ]);
    }

    #[Route('/membre/download/facture/{tokenDocument}', name: 'member_facture_download')]
    public function factureDownload($tokenDocument, Request $request)
    {
        $user = $this->security->getUser();

        $document = $this->documentRepository->findOneBy(['token' => $tokenDocument, 'user' => $user]);

        if(!$document){

            $tableau = [
                'h1' => 'Document non trouvé !',
                'p1' => 'La consultation de ce document est impossible!',
                'p2' => 'Document inconnu, supprimé ou ne vous appartenant pas!'
            ];

            return $this->render('site/document_view/_end_view.html.twig', [
                'tableau' => $tableau
            ]);

        }else{

            $this->documentService->generatePdf($document, $request);

            return new Response();

            // $results = $this->documentService->generateValuesForDocument($document);
            // $legales = $this->legalInformationRepository->findOneBy([]);

            // return $this->render('site/document_download/_document_download.html.twig', [
            //     'document' => $document,
            //     'legales' => $legales,
            //     'css' => file_get_contents('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css'),
            //     "docLines" => $document->getDocumentLines(),
            //     "tva" => $results['tauxTva'],
            //     "docLine_items" => $results['docLine_items'],
            //     "docLine_occasions" => $results['docLine_occasions'],
            //     "docLine_boites" => $results['docLine_boites']
            // ]);
        }
    }
}

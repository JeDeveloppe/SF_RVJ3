<?php

namespace App\Controller\Member;

use DateInterval;
use App\Form\UserType;
use DateTimeImmutable;
use App\Service\Utilities;
use App\Service\DocumentService;
use App\Entity\DocumentParametre;
use App\Service\UtilitiesService;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\AddressRepository;
use App\Repository\AdresseRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConfigurationRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\DocumentLignesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\DocumentParametreRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        private EntityManagerInterface $em
        )
    {
    }

    #[Route('/membre', name: 'member')]
    public function index(): Response
    {

        return $this->render('member/index.html.twig', []);
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

    #[Route('/membre/historique', name: 'member_historique')]
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
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->security->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
dd($user);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $userRepository->add($user);
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

    #[Route('/membre/download/facture/{token}', name: 'member_facture_download')]
    public function factureDownload($token, DocumentService $documentService)
    {
        $documentService->factureToPdf($token);

        return new Response();
    }
}

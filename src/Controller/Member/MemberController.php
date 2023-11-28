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
use App\Repository\ConfigurationRepository;
use App\Repository\DocumentLignesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\DocumentParametreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MemberController extends AbstractController
{

    public function __construct(
        private DocumentRepository $documentRepository,
        private Security $security,
        private UtilitiesService $utilitiesService,
        private PanierRepository $panierRepository,
        private AddressRepository $addressRepository,
        private DocumentService $documentService,
        private EntityManagerInterface $em
        )
    {
    }

    #[Route('/membre', name: 'app_member')]
    public function index(): Response
    {

        return $this->render('member/index.html.twig', []);
    }

    #[Route('/membre/adresses', name: 'app_member_adresses')]
    public function membreAdresses(): Response
    {
        $user = $this->security->getUser();

        return $this->render('member/adresse/index.html.twig', [
            'livraison_adresses' => $this->addressRepository->findBy(['user' => $user, 'isFacturation' => false]),
            'facturation_adresses' => $this->addressRepository->findBy(['user' => $user, 'isFacturation' => true]),
        ]);

    }

    #[Route('/membre/historique', name: 'app_member_historique')]
    public function membreHistorique(DocumentParametreRepository $documentParametreRepository): Response
    {
        $user = $this->security->getUser();
                
        $documents = $user->getDocuments();

        return $this->render('member/historique.html.twig', [
            'documents' => $documents,
            'docParams' => $documentParametreRepository->findOneBy([])
        ]);
    }

    #[Route('/membre/mon-compte', name: 'app_member_compte')]
    public function membreCompte(
        Request $request,
        UserRepository $userRepository,): Response
    {
        $user = $this->security->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_member_compte', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('member/compte.html.twig', [
            'form' => $form->createView()
            ]);
    }

    #[Route('/membre/download/facture/{token}', name: 'app_member_facture_download')]
    public function factureDownload($token, DocumentService $documentService)
    {
        $documentService->factureToPdf($token);

        return new Response();
    }
}

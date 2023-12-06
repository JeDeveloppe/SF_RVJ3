<?php

namespace App\Controller\Site;

use App\Form\ContactType;
use App\Entity\ResetPassword;
use App\Service\PanierService;
use App\Form\ResetPasswordType;
use App\Service\DocumentService;
use App\Service\PasswordService;
use App\Service\UtilitiesService;
use App\Repository\UserRepository;
use App\Repository\PartnerRepository;
use Symfony\Component\Form\FormError;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentLineRepository;
use App\Form\EmailForSendResetPasswordType;
use App\Repository\ResetPasswordRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LegalInformationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SiteController extends AbstractController
{
    public function __construct(
        private LegalInformationRepository $legalInformationRepository,
        private PanierService $panierService,
        private DocumentService $documentService,
        private UtilitiesService $utilitiesService,
        private UserRepository $userRepository,
        private PasswordService $passwordService,
        private ResetPasswordRepository $resetPasswordRepository
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

    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(Request $request): Response
    {

        $form = $this->createForm(EmailForSendResetPasswordType::class, null);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $this->userRepository->findOneBy(['email' => $form->get('email')->getData()]);

            if(!$user){

                $form->get('email')->addError(new FormError('Aucun compte n\'est associé à cette adresse email...'));

            }else{

                $resetPassword = new ResetPassword();
                $resetPassword->setEmail($form->get('email')->getData());
                $this->passwordService->saveResetPasswordInDatabaseAndSendEmail($resetPassword);

                $this->addFlash('success', 'Un lien viens de vous être envoyé...');
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('site/password/email_to_send_link_for_reset_password.html.twig', [
            'emailForSendResetPasswordForm' => $form->createView()
        ]);
    }

    #[Route('/reset-password/{uuid}', name: 'reset_password')]
    public function resetPassword($uuid, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        $resetPassword = $this->resetPasswordRepository->findOneBy(['uuid' => $uuid]);

        if(!$resetPassword OR $resetPassword->isIsUsed() != false){

            $this->addFlash('warning', 'Demande inconnue ou déjà utilisée !');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(ResetPasswordType::class, null);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if($form->get('password')->getData() !== $form->get('passwordVerify')->getData()){

                $form->get('password')->addError(new FormError('Les mots de passe ne sont pas identiques...'));

            }else{

                // encode the plain password
                $user = $this->userRepository->findOneBy(['email' => $resetPassword->getEmail()]);
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                    );

                $entityManager->persist($user);

                //update invitation
                $resetPassword->setIsUsed(true);

                $entityManager->persist($resetPassword);

                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe mis à jour !');
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('site/password/reset_password.html.twig', [
            'resetPasswordForm' => $form->createView()
        ]);
    }
}
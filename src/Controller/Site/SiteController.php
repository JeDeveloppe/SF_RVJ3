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
use App\Form\EmailForSendResetPasswordType;
use App\Repository\ResetPasswordRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LegalInformationRepository;
use App\Service\MailService;
use App\Service\PartnerService;
use App\Service\UserService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('v3', name: '')]
class SiteController extends AbstractController
{
    public function __construct(
        private LegalInformationRepository $legalInformationRepository,
        private PanierService $panierService,
        private MailService $mailService,
        private DocumentService $documentService,
        private UtilitiesService $utilitiesService,
        private UserRepository $userRepository,
        private PasswordService $passwordService,
        private DocumentRepository $documentRepository,
        private UserService $userService,
        private ResetPasswordRepository $resetPasswordRepository,
        private PartnerService $partnerService
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
    public function partenaires(Request $request, PartnerRepository $partnerRepository): Response
    {
        $partenaires = $partnerRepository->findBy(['isOnline' => true], ['name' => 'ASC']);
        
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        $donnees = $this->partnerService->constructionMapOfFranceWithPartners($baseUrl);

        return $this->render('site/partner/partners.html.twig', [
            'donnees' => $donnees,
            'partners' => $partenaires
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
    
        if($form->isSubmitted() && $form->isValid()) {
    
            $legales = $this->legalInformationRepository->findOneBy([]);

            $this->mailService->sendMail(
                $legales->getEmailCompany(),
                "Message du site en date du ".(new DateTimeImmutable('now'))->format('d-m-Y').": ".$form->get('sujet')->getData(),
                'contact',
                [
                    'mail' => $form->get('email')->getData(),
                    'question' => $form->get('message')->getData(),
                    'legales' => $legales
                ],
                $form->get('email')->getData(),
                false
            );
    
            $this->addFlash('success', 'Message bien envoyé!');
            return $this->redirectToRoute('app_contact');
        }
    
        return $this->render('site/contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/merci', name: 'app_thanks')]
    public function tanks(Request $request): Response
    {
        
        $donnees = $this->userService->constructionMapOfFranceWithUserWhoHaveCommanded();

        return $this->render('site/thanks/thanks.html.twig', [
            'donnees' => $donnees
        ]);

    }

    #[Route('/document/{tokenDocument}', name: 'document_view')]
    public function lectureDevis(
        $tokenDocument,
        ): Response
    {

        $document = $this->documentRepository->findOneBy(['token' => $tokenDocument]);

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

            $results = $this->documentService->generateValuesForDocument($document);

            return $this->render('site/document_view/_document_view.html.twig', [
                'document' => $document,
                'docLine_items' => $results['docLine_items'],
                'docLine_occasions' => $results['docLine_occasions'],
                'docLine_boites' => $results['docLine_boites'],
                'tva' => $results['tauxTva']
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
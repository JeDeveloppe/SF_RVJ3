<?php

namespace App\Controller\Site;

use DateTimeImmutable;
use App\Form\ContactType;
use App\Service\MailService;
use App\Service\UserService;
use App\Entity\ResetPassword;
use App\Service\PanierService;
use App\Form\ResetPasswordType;
use App\Service\PartnerService;
use App\Service\DocumentService;
use App\Service\PasswordService;
use App\Service\UtilitiesService;
use App\Repository\UserRepository;
use App\Repository\MediaRepository;
use App\Form\AddressForDonationType;
use App\Repository\PartnerRepository;
use Symfony\Component\Form\FormError;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EmailForSendResetPasswordType;
use App\Repository\AmbassadorRepository;
use App\Repository\ResetPasswordRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LegalInformationRepository;
use App\Service\AmbassadorService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


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
        private PartnerService $partnerService,
        private PartnerRepository $partnerRepository,
        private AmbassadorService $ambassadorService
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $metas['description'] = 'Vous avez un jeu de société incomplet ? Refaites vos jeux vous propose un service pour donner une seconde vie à votre jeu, nous avons plein de pièces détachées en stock.';

        return $this->render('site/index.html.twig', [
            'metas' => $metas
        ]);
    }

    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function mentionsLegales(): Response
    {
        $legales = $this->legalInformationRepository->findOneBy(['isOnline' => true], ['id' => 'ASC']);
        $metas['description'] = 'Vous avez un jeu de société incomplet ? Refaites vos jeux vous propose un service pour donner une seconde vie à votre jeu, nous avons plein de pièces détachées en stock.';

        return $this->render('site/legale/mentions_legales.html.twig', [
            'legales' => $legales,
            'metas' => $metas
        ]);
    }

    #[Route('/conditions-generale-de-vente', name: 'app_conditions_generale_de_vente')]
    public function cgv(): Response
    {
        $legales = $this->legalInformationRepository->findOneBy(['isOnline' => true], ['id' => 'ASC']);
        $metas['description'] = 'Nos conditions générales de ventes concernant le site.';

        return $this->render('site/legale/cgv.html.twig', [
            'legales' => $legales,
            'metas' => $metas
        ]);
    }

    #[Route('/nos-partenaires', name: 'app_partenaires')]
    public function partenaires(Request $request, PartnerRepository $partnerRepository): Response
    {
        $partenaires = $partnerRepository->findBy(['isOnline' => true], ['name' => 'ASC']);
        
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        $donnees = $this->partnerService->constructionMapOfFranceWithPartners($baseUrl);

        $metas['description'] = 'Cette page répertorie tous les partenaires français du service. Il s’agit de personnes, d’organismes ou d’entreprises qui s’inscrivent dans la même démarche autour du jeu, du développement durable, du réemploi et de la réduction des déchets. Auprès de ces partenaires vous pouvez acheter, louer ou donner des jeux d’occasion !';

        return $this->render('site/partner/partners.html.twig', [
            'donnees' => $donnees,
            'partners' => $partenaires,
            'metas' => $metas
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        $metas['description'] = 'Si vous avez la moindre question sur le site, une demande de partenariat ou autre, n\'hésitez pas !';
    
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
            'metas' => $metas
        ]);
    }

    #[Route('/coin-presse', name: 'app_press')]
    public function press(MediaRepository $mediaRepository): Response
    {
        $metas['description'] = 'Quelques chiffres et liens de notre présence sur internet.';
        $medias = $mediaRepository->findBy(['isOnLine' => true],['publishedAt' => 'DESC']);
        
        return $this->render('site/press/press.html.twig', [
            'metas' => $metas,
            'medias' => $medias
        ]);

    }

    #[Route('/projet/qui-sommes-nous', name: 'app_who_are_we')]
    public function whoAreWe(): Response
    {
        $metas['description'] = 'Une petite description de ce qu\'est le service de Refaites vos jeux';
        
        return $this->render('site/project/qui_sommes_nous.html.twig', [
            'metas' => $metas
        ]);

    }

    #[Route('/projet/nous-soutenir/devenir-ambassadeur-ambassadrice', name: 'app_became_ambassador')]
    public function becameAmbassador(Request $request): Response
    {
        
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        $donnees = $this->ambassadorService->constructionMapOfFranceWithAmbassadors($baseUrl);

        //TODO
        $metas['description'] = 'Description à faire';
        
        return $this->render('site/project/nous_soutenir/devenir-ambassadeur.html.twig', [
            'metas' => $metas,
            'donnees' => $donnees
        ]);

    }

    #[Route('/projet/nous-soutenir/acheter-des-jeux', name: 'app_buy_games')]
    public function buyGames(): Response
    {
        
        //TODO
        $metas['description'] = 'Description à faire';
        
        return $this->render('site/project/nous_soutenir/acheter-des-jeux.html.twig', [
            'metas' => $metas,
        ]);

    }

    #[Route('/projet/nous-soutenir/adherer-a-l-association', name: 'app_adherer')]
    public function adherer(Request $request): Response
    {

        //TODO
        $metas['description'] = 'Description à faire';
        
        return $this->render('site/project/nous_soutenir/adherer.html.twig', [
            'metas' => $metas,            
            'legales' => $this->legalInformationRepository->findOneBy([])
        ]);

    }

    #[Route('/projet/nous-soutenir/nos-prestations', name: 'app_prestations')]
    public function prestations(Request $request): Response
    {

        //TODO
        $metas['description'] = 'Description à faire';
        
        return $this->render('site/project/prestations.html.twig', [
            'metas' => $metas,            
            'legales' => $this->legalInformationRepository->findOneBy([])
        ]);

    }

    #[Route('/projet/nous-soutenir/faire-un-don', name: 'app_make_donation')]
    public function makeDonation(Request $request): Response
    {
        
        $form = $this->createForm(AddressForDonationType::class);
        $form->handleRequest($request);

        //TODO IF PAYPLUG FOR DONATION
        if($form->isSubmitted() && $form->isValid()) {

        }

        //TODO
        $metas['description'] = 'Description à faire';
        
        return $this->render('site/project/nous_soutenir/faire-un-don.html.twig', [
            'metas' => $metas,            
            'legales' => $this->legalInformationRepository->findOneBy([]),
            'form' => $form
        ]);

    }

    #[Route('/projet/nous-soutenir/donner-ses-jeux', name: 'app_give_your_games')]
    public function giveYourGames(): Response
    {
        
        //TODO
        $metas['description'] = 'Description à faire';
        
        return $this->render('site/project/nous_soutenir/donner-ses-jeux.html.twig', [
            'metas' => $metas,
            'legales' => $this->legalInformationRepository->findOneBy([])
        ]);

    }

    #[Route('/nous-soutenir', name: 'app_support_us')]
    public function supportUs(Request $request): Response
    {
  
        $metas['description'] = 'Description à faire';

        $supports = [
            [
                'text' => 'Acheter des jeux',
                'link' => 'app_buy_games'
            ],
            [
                'text' => 'Donner ses jeux',
                'link' => 'app_give_your_games'
            ],
            [
                'text' => 'Devenir ambassadeur / trice',
                'link' => 'app_became_ambassador'
            ],
            [
                'text' => 'Faire un don',
                'link' => 'app_make_donation'
            ],
            [
                'text' => 'A faire',
                'link' => 'app_buy_games'
            ]
        ];


        return $this->render('site/project/nous_soutenir.html.twig', [
            'metas' => $metas,
            'supports' => $supports,
        ]);

    }

    #[Route('/document/{tokenDocument}', name: 'document_view')]
    public function lectureDevis(
        $tokenDocument,
        ): Response
    {

        $document = $this->documentRepository->findOneBy(['token' => $tokenDocument]);

        if(!$document){

            return $this->documentService->renderIfDocumentNoExist();

        }else{

            $results = $this->documentService->generateValuesForDocument($document);
            return $this->render('site/document_view/_document_view.html.twig', [
                'document' => $document,
                'docLines' => $results,
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

        if($form->isSubmitted() && $form->isValid())
        {

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
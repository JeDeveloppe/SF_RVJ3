<?php

namespace App\Controller;

use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private AddressRepository $addressRepository
    )
    {}

    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $metas['description'] = 'Connectez-vous a votre espace membre pour pouvoir profiter de nos services';


        return $this->render('site/pages/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'metas' => $metas]);
    }

    #[Route(path: 'logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/delete-user', name: 'delete_user_from_database')]
    public function deleteUserFromDatabase(Request $request): Response
    {
        $user = $this->security->getUser();
        $session = $request->getSession();
        $session->clear();

        //? on cherche toutes les adresses de l'utilisateur et on les supprimes
        $adress = $this->addressRepository->findByUser($user);
        foreach($adress as $adres){
            $this->em->remove($adres);
        }

        //?on vide le passier en session

        $token = $this->container->get('security.token_storage');
        $token->setToken(null);

        $this->em->remove($user);
        $this->em->flush();
        
        // Ceci ne fonctionne pas avec la création d'une nouvelle session !
        $this->addFlash('success', 'Votre compte utilisateur a bien été supprimé !'); 
        
        return $this->redirectToRoute('app_home');
    }


}

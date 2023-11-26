<?php

namespace App\Controller\Site;

use App\Repository\DocumentRepository;
use App\Repository\PanierRepository;
use Exception;
use App\Service\PaiementService;
use App\Service\UtilitiesService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class PaiementController extends AbstractController
{

    public function __construct(
        private PaiementService $paiementService,
        private PanierRepository $panierRepository,
        private Security $security,
        private UtilitiesService $utilitiesService,
        private DocumentRepository $documentRepository
    ){  
    }

    #[Route('/paiement/{tokenDocument}', name: 'app_paiement')]
    public function creationPaiement($tokenDocument)
    {
        if($_ENV["PAIEMENT_MODULE"] == "STRIPE")
        {
            $session = $this->paiementService->creationPaiementWithStripe($tokenDocument);
            return $this->redirect($session->url, 303);

        }else if($_ENV["PAIEMENT_MODULE"] == "PAYPLUG")
        {
            $payment_url = $this->paiementService->creationPaiementWithPayplug($tokenDocument);
            return $this->redirect($payment_url, 303);

        }else{
            throw new Exception('PAIEMENT_MODULE IN .ENV FILE NOT INFORM');
        }
    }

    #[Route('/paiement/validation/{tokenDocument}', name: 'app_paiement_success')]
    public function paiementSuccess($tokenDocument)
    {
        if($_ENV["PAIEMENT_MODULE"] == "STRIPE")
        {
            $this->paiementService->paiementSuccessWithStripe($tokenDocument);
        }else if($_ENV["PAIEMENT_MODULE"] == "PAYPLUG")
        {
            $response = $this->paiementService->paiementSuccessWithPayplug($tokenDocument);

            //si on a bien vérifié le paiement
            if(array_key_exists('paiement', $response)){
                return $this->render('site/paiement/success.html.twig', [
                    'token' => $tokenDocument,
                ]);
            }else{
                $this->addFlash('warning', $response['messageFlash']);
                return $this->redirectToRoute($response['route']);
            }


        }else{
            throw new Exception('PAIEMENT_MODULE IN .ENV FILE NOT INFORM');
        }
    }

    #[Route('/paiement/annulation-achat/{tokenDocument}', name: 'app_paiement_canceled')]
    public function paiementCancel($tokenDocument)
    {
        $document = $this->documentRepository->findOneBy(['token' => $tokenDocument]);

        if(!$document){
            //pas de devis
            $this->addFlash('warning', 'Document inconnu!');
            return $this->redirectToRoute('accueil');

        }else{

            return $this->render('site/paiement/cancel.html.twig', [
                'token' => $tokenDocument,
            ]);
        }
    }

    //TODO
    #[Route('/paiement/notificationUrl/{tokenDocument}', name: 'app_paiement_notificationUrl')]
    public function notificationUrl($tokenDocument)
    {
        if($_ENV["PAIEMENT_MODULE"] == "STRIPE")
        {
            $this->paiementService->notificationUrlWithStripe($tokenDocument);
        }else if($_ENV["PAIEMENT_MODULE"] == "PAYPLUG")
        {
            $this->paiementService->notificationUrlWithPayplug($tokenDocument);
        }else{
            throw new Exception('PAIEMENT_MODULE IN .ENV FILE NOT INFORM');
        }
    }
}
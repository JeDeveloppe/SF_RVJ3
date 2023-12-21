<?php

namespace App\Service;

use Exception;
use DateInterval;
use Stripe\Stripe;
use DateTimeImmutable;
use App\Entity\Payment;
use App\Repository\DocumentParametreRepository;
use App\Repository\DocumentRepository;
use App\Repository\DocumentStatusRepository;
use App\Repository\PaiementRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MeansOfPayementRepository;
use App\Repository\PaymentRepository;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaiementService
{

    public function __construct(
        private EntityManagerInterface $em,
        private UtilitiesService $utilities,
        private DocumentService $documentService,
        private DocumentRepository $documentRepository,
        private DocumentParametreRepository $documentParametreRepository,
        private UrlGeneratorInterface $urlGeneratorInterface,
        private Security $security,
        private RouterInterface $router,
        private MeansOfPayementRepository $meansOfPayementRepository,
        private PaymentRepository $paymentRepository,
        private DocumentStatusRepository $documentStatusRepository
        ){
    }

    public function creationPaiementWithStripe($token): Response
    {

        $document = $this->documentRepository->findOneBy(['token' => $token, 'numeroFacture' => NULL, 'paiement' => NULL]);

        if(!$document){
            //pas de devis
            $this->addFlash('warning', 'Devis inconnu!');
            return $this->Router->redirectToRoute('accueil');
        }

        //on s'identifie
        $this->stripeAuth();

        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    
                    'product_data' => [
                            'name' => 'Devis '.$document->getNumeroDevis(),
                        ],
                    'unit_amount' => $document->getTotalTTC(),
                ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->router->generateUrl('paiement_success', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->router->generateUrl('paiement_canceled', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
          ]);

        // //on renseigne le paiement
        // $paiement = new Paiement();
        // $paiement->setDocument($document)
        //         ->setTokenTransaction($payment_id)
        //         ->setCreatedAt(new DateTimeImmutable('now'));
        // //on sauvegarde le paiement
        // $this->em->persist($paiement);
        // $this->em->flush();

        // //on met a jour le document lui meme
        // $document->setPaiement($paiement);
        // $this->em->merge($document);
        // $this->em->flush();

        return $session;
    }

    public function creationPaiementWithPayplug($token)
    {

        $document = $this->documentRepository->findOneBy(['token' => $token, 'billNumber' => NULL, 'isDeleteByUser' => false]);

        // if(!$document){
        //     //pas de devis
        //     $this->flashBagInterface->getFlashBag->add('warning', 'Devis inconnu!');
        //     $this->flashBagInterface->add('warning', 'Devis inconnu!');
        //     return $this->router->generate('accueil');
        // }

        //on s'identifie
        $this->payplugAuth();

        $customer_id = $document->getToken();
        
        $arrayAdresseF = $this->explodeAdresse($document->getBillingAddress(),$document);
        $arrayAdresseL = $this->explodeAdresse($document->getDeliveryAddress(),$document);

        $payment = \Payplug\Payment::create([
                'amount'            => $document->getTotalWithTax(),
                'currency'          => 'EUR',
                'billing'          => [
                    'title'        => $arrayAdresseF['title'],
                    'first_name'   => $arrayAdresseF['first_name'],
                    'last_name'    => $arrayAdresseF['last_name'],
                    'email'        => $arrayAdresseF['email'],
                    'address1'     => $arrayAdresseF['adresse1'],
                    'postcode'     => $arrayAdresseF['postCode'],
                    'city'         => $arrayAdresseF['city'],
                    'country'      => $arrayAdresseF['country'],
                    'language'     => $arrayAdresseF['language']
                ],
                'shipping'          => [
                    'title'        => $arrayAdresseL['title'],
                    'first_name'   => $arrayAdresseL['first_name'],
                    'last_name'    => $arrayAdresseL['last_name'],
                    'email'        => $arrayAdresseL['email'],
                    'address1'     => $arrayAdresseL['adresse1'],
                    'postcode'     => $arrayAdresseL['postCode'],
                    'city'         => $arrayAdresseL['city'],
                    'country'      => $arrayAdresseL['country'],
                    'language'     => $arrayAdresseL['language'],
                    'delivery_type' => 'BILLING'
                ],
                'hosted_payment' => [
                    'return_url' => $this->urlGeneratorInterface->generate('paiement_success', ['tokenDocument' => $customer_id], UrlGeneratorInterface::ABSOLUTE_URL),
                    'cancel_url' => $this->urlGeneratorInterface->generate('paiement_canceled', ['tokenDocument' => $customer_id], UrlGeneratorInterface::ABSOLUTE_URL)
                ],
                'notification_url' => $this->urlGeneratorInterface->generate('paiement_notificationUrl', ['tokenDocument' => $customer_id], UrlGeneratorInterface::ABSOLUTE_URL),
                'metadata'         => [
                    'customer_id'  => $customer_id
                ]
        ]);

        $payment_url = $payment->hosted_payment->payment_url;
        $payment_id = $payment->id;



        $paiement = $this->paymentRepository->findOneBy(['document' => $document]);

        if(!$paiement){
            $paiement = new Payment();
        }

        //on renseigne le paiement
        $paiement->setDocument($document)
                ->setMeansOfPayment($this->meansOfPayementRepository->findOneBy(['name' => 'CB']))
                ->setTokenPayment($payment_id)
                ->setCreatedAt(new DateTimeImmutable('now'));
        //on sauvegarde le paiement
        $this->em->persist($paiement);
        $this->em->flush();

        return $payment_url;
    }

    public function paiementSuccessWithStripe($token)
    {
        throw new Exception('function paiementSuccessWithStripe in paiementService NOT INFORM');
    }
    
    public function paiementSuccessWithPayplug($token)
    {
        $response = [];

        $document = $this->documentRepository->findOneBy(['token' => $token]);
        $docParams = $this->documentParametreRepository->findOneBy([]);

        if(!$document){
            //pas de devis
            $response['messageFlash'] = 'Document inconnu!';
            $response['route'] = 'accueil';

            return $response;

        }else if(!is_null($document->getBillNumber())){
            //document deja facturé
            $response['paiement'] = 'Document déjà payé!';

            return $response;

        }else if(is_null($document->getBillNumber())){

            //on s'identifie
            $this->payplugAuth();
            //on interroge le paiement
            $payment = \Payplug\Payment::retrieve($document->getPayment()->getTokenPayment());

            if($payment->is_paid){

                $payment_id = $payment->id;
                $payment_date = $this->utilities->getDateTimeImmutableFromTimestamp($payment->hosted_payment->paid_at);
                $card = $payment->card->brand.'(***** '.$payment->card->last4.' - '.$payment->card->exp_month.'/'.$payment->card->exp_year.')';

                //on retrouve le paiement deja lier
                $paiement = $document->getPayment();
                //il faut creer le numero de facture
                $newNumero = $this->documentService->generateNewNumberOf('billNumber', 'getBillNumber');

                //on renseigne le paiement
                $paiement->setDetails($card)
                        ->setTimeOfTransaction($payment_date);
                //on sauvegarde le paiement
                $this->em->persist($paiement);
                $this->em->flush();
                
                //on met a jour le document en BDD
                $etat = $this->documentStatusRepository->findOneBy(['action' => 'TO_PREPARE']);
                $document->setDocumentStatus($etat)->setBillNumber($docParams->getBillingTag().$newNumero);
                $this->em->persist($document);
                $this->em->flush();

                $response['paiement'] = true;
                return $response;

            }else{

                //document non payé
                return $this->urlMatcherInterface->redirectToRoute('paiement_canceled');

            }
        }
    }

    public function notificationUrlWithPayplug($token)
    {
        //TODO
        //on s'identifie
        $this->payplugAuth();

        $docParams = $this->documentParametreRepository->findOneBy([]);

        $input = file_get_contents('php://input');

            $resource = \Payplug\Notification::treat($input);

            if ($resource instanceof \Payplug\Resource\Payment
                && $resource->is_paid) {

                //id de paiement payplug
                $payment_id = $resource->id;
                //on retrouve le paiement et le document
                $paiement = $this->paymentRepository->findOneBy(['tokenPayment' => $payment_id]);
                $document = $paiement->getDocument();

                $payment_date = $this->utilities->getDateTimeImmutableFromTimestamp($resource->hosted_payment->paid_at);
                $card = $resource->card->brand.'(***** '.$resource->card->last4.' - '.$resource->card->exp_month.'/'.$resource->card->exp_year.')';

                //on retrouve le paiement deja lier
                $paiement = $document->getPayment();
                //il faut creer le numero de facture
                $newNumero = $this->documentService->generateNewNumberOf('billNumber', 'getBillNumber');
               //on renseigne le paiement
                $paiement->setDetails($card)
                    ->setTimeOfTransaction($payment_date);
                //on sauvegarde le paiement
                $this->em->persist($paiement);
                $this->em->flush();
                
                //on met a jour le document en BDD
                $etat = $this->documentStatusRepository->findOneBy(['action' => 'TO_PREPARE']);
                $document->setDocumentStatus($etat)->setBillNumber($docParams->getBillingTag().$newNumero);
                $this->em->persist($document);
                $this->em->flush();
            }
    }

    public function notificationUrlWithStripe($token)
    {
        throw new Exception('function notificationUrlWithStripe in paiementService NOT INFORM');
    }

    private function explodeAdresse($adresse,$document)
    {        

        $adresseExploded = explode("<br/>", $adresse);
        $arrayAdresse = [];

        //si on a une association
        if(count($adresseExploded) > 4){
            $arrayAdresse['title'] = $adresseExploded[0]; //association
            $first_last = explode(" ", $adresseExploded[1]); // prénom et nom
            $arrayAdresse['first_name']  = $first_last[0];
            $arrayAdresse['last_name'] = $first_last[1];
            $arrayAdresse['email'] = $document->getUser()->getEmail();
            $arrayAdresse['adresse1'] = $adresseExploded[2];
            $postal_ville = explode(" ", $adresseExploded[3]);
            $arrayAdresse['postCode'] = $postal_ville[0];
            $arrayAdresse['city'] = $postal_ville[1];
            $arrayAdresse['country'] = $adresseExploded[4];
            $arrayAdresse['language'] = 'fr';
        }else{
            $arrayAdresse['title'] = "Mr / Mme";
            $first_last = explode(" ", $adresseExploded[0]); // prénom et nom
            $arrayAdresse['first_name']  = $first_last[0];
            $arrayAdresse['last_name'] = $first_last[1];
            $arrayAdresse['email'] = $document->getUser()->getEmail();
            $arrayAdresse['adresse1'] = $adresseExploded[1];
            $postal_ville = explode(" ", $adresseExploded[2]);
            $arrayAdresse['postCode'] = $postal_ville[0];
            $arrayAdresse['city'] = $postal_ville[1];
            $arrayAdresse['country'] = $adresseExploded[3];
            $arrayAdresse['language'] = 'fr';
        }

        return $arrayAdresse;
    }
    
    public function payplugAuth()
    {
        \Payplug\Payplug::init(['secretKey' => $_ENV["PAYPLUG_SECRET"]]);
    }

    public function stripeAuth()
    {
        $stripe = new Stripe();
        $stripe->setApiKey($_ENV["STRIPE_SECRET"]);
    }
}
<?php

namespace App\Controller\Site;

use App\Service\PanierService;
use App\Service\OccasionService;
use App\Repository\TaxRepository;
use App\Repository\BoiteRepository;
use App\Repository\EditorRepository;
use App\Repository\PartnerRepository;
use App\Repository\OccasionRepository;
use App\Form\SearchBoiteInCatalogueType;
use App\Form\SearchOccasionInCatalogueType;
use App\Repository\AddressRepository;
use App\Repository\CollectionPointRepository;
use App\Repository\SiteSettingRepository;
use App\Service\AdresseService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class CatalogController extends AbstractController
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private OccasionRepository $occasionRepository,
        private PaginatorInterface $paginator,
        private EditorRepository $editorRepository,
        private PartnerRepository $partnerRepository,
        private TaxRepository $taxRepository,
        private PanierService $panierService,
        private OccasionService $occasionService,
        private AddressRepository $addressRepository,
        private CollectionPointRepository $collectionPointRepository,
        private AdresseService $adresseService,
        private SiteSettingRepository $siteSettingRepository
    )
    {
    }
    
    #[Route('/catalogue-pieces-detachees', name: 'app_catalogue_pieces_detachees')]
    public function cataloguePiecesDetachees(Request $request): Response
    {

        $form = $this->createForm(SearchBoiteInCatalogueType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('search')->getData();
            $phrase = str_replace(" ","%",$search);

            $donneesFromDatabases = $this->boiteRepository->findItemsFromBoitesFromSearch($phrase);

        }else{

            //$donneesFromDatabases = $this->boiteRepository->findBy(['isOnline' => true],['id' => 'DESC']);
            $donneesFromDatabases = $this->boiteRepository->findBoitesWhereThereIsItems();

        }

        //on tri uniquement les donnees avec articles
        $donnees = [];

        foreach($donneesFromDatabases as $donneesFromDatabase){
            if(count($donneesFromDatabase->getItemsOrigine()) > 0 OR count($donneesFromDatabase->getItemsSecondaire()) > 0){

                array_push($donnees,$donneesFromDatabase);

            }
        }

        $boites = $this->paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            24 /*limit per page*/
        );

        //? effet rotation X ou Y aléatoire sur les cards
        $class_transforms = [['transformX','backX'],['transformY','backY']];
        for($i=0;$i<count($boites);$i++){
            shuffle($class_transforms);
            $transforms[] = $class_transforms[0];
        }

        $partenaires = $this->partnerRepository->findBy(['isDisplayOnCatalogueWhenSearchIsNull' => true]);


        $metas['description'] = 'Catalogue complet de toutes les boites dont le service dispose de pièces détachées.';

        return $this->render('site/catalog/pieces_detachees/les_pieces_detachees.html.twig', [
            'boites' => $boites,
            'boites_totales' => $donnees,
            'form' => $form,
            'search' => $search ?? null,
            'partenaires' => $partenaires ?? null,
            'transforms' => $transforms ?? null,
            'metas' => $metas,
            'tax' => $this->taxRepository->findOneBy([])

        ]);
    }


    #[Route('/catalogue-pieces-detachees/{editorSlug}/{id}/{slug}', name: 'catalogue_pieces_detachees_demande')]
    public function cataloguePiecesDetacheesDemande($id, $slug, $editorSlug, Request $request): Response
    {

        $boite = $this->boiteRepository->findOneBy(['id' => $id, 'slug' => $slug, 'editor' => $this->editorRepository->findOneBy(['slug' => $editorSlug]), 'isOnline' => true]);

        if(!$boite){
            $this->addFlash('warning', 'Boite inconnue');
            return $this->redirectToRoute('app_catalogue_pieces_detachees');
        }

        $metas['description'] = 'Les pièces détachées que le service a en stock concernant le jeu: '.$boite->getName().' - '.$boite->getEditor()->getName();

        $items = $boite->getItemsOrigine();
        $groups = [];

        foreach($items as $item){
            if(!array_key_exists($item->getItemGroup()->getId(),$groups)){
                $groups[$item->getItemGroup()->getId()] = [
                    'group' => $item->getItemGroup(),
                    'items' => [
                        $item
                    ]
                ];
            } else {
                $groups[$item->getItemGroup()->getId()]['items'][] = $item;
            }
        }

        return $this->render('site/catalog/pieces_detachees/pieces_detachees_demande.html.twig', [
            'boite' => $boite,
            'metas' => $metas,
            'groups' => $groups,
            'tax' => $this->taxRepository->findOneBy([]),
        ]);
    }

    #[Route('/catalogue-jeux-occasion', name: 'app_catalogue_occasions')]
    public function catalogueOccasions(Request $request): Response
    {
        $form = $this->createForm(SearchOccasionInCatalogueType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {
            
            $donneesFromDatabases = $this->occasionService->findOccasionsFromOccasionForm($form);

        }else{

            $donneesFromDatabases = $this->occasionRepository->findBy(['isOnline' => true],['id' => 'DESC']);

        }


        $occasions = $this->paginator->paginate(
            $donneesFromDatabases, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            24 /*limit per page*/
        );

        //? effet rotation X ou Y aléatoire sur les cards
        $class_transforms = [['transformX','backX'],['transformY','backY']];
        for($i=0;$i<count($occasions);$i++){
            shuffle($class_transforms);
            $transforms[] = $class_transforms[0];
        }

        $metas['description'] = 'Catalogue complet des jeux d\'occasion disponible à la vente en retrait sur Caen.';

        return $this->render('site/catalog/occasions/les_occasions.html.twig', [
            'occasions' => $occasions,
            'occasions_totales' => $donneesFromDatabases,
            'transforms' => $transforms ?? null,
            'metas' => $metas,
            'form' => $form,
            'tax' => $this->taxRepository->findOneBy([]),
        ]);
    }

    #[Route('/jeu-occasion/{reference_occasion}/{editor_slug}/{boite_slug}', name: 'occasion')]
    public function occasion($reference_occasion, Security $security, $editor_slug): Response
    {

        $occasion = $this->occasionRepository->findOneBy(
            [
                'isOnline' => true,
                'reference' => $reference_occasion
            ]);

        if(!$occasion){

            $this->addFlash('warning', 'Jeux non disponible ou inconnu !');
            return $this->redirectToRoute('app_catalogue_occasions');
        }

        $user = $security->getUser();

        if(!$user){

            $delivery = null;

        }else{

            $deliveryAdresse = $this->addressRepository->findOneBy(['user' => $user, 'isFacturation' => false]);
            $collectionPoint = $this->collectionPointRepository->findOneCollectionPointForOccasionBuy();
            $kmsBetweenCollectionPointAndDeliveryAdress = $this->adresseService->get_distance_from_collectePoint($collectionPoint, $deliveryAdresse);
            
            $setting = $this->siteSettingRepository->findOneBy([]);

            if($kmsBetweenCollectionPointAndDeliveryAdress < $setting->getDistanceMaxForOccasionBuy()){

                $delivery = true;

            }else{

                $delivery = false;

            }
        }

        $metas['description'] = 'Jeu d\'occasion vérifié, remis en état, et disponible à petit prix: '.$occasion->getBoite()->getName().' - '.$occasion->getBoite()->getEditor()->getName();

        return $this->render('site/catalog/occasions/occasion.html.twig', [
            'occasion' => $occasion,
            'tax' => $this->taxRepository->findOneBy([]),
            'metas' => $metas,
            'delivery' => $delivery
        ]);
    }
}

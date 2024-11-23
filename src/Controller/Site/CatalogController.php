<?php

namespace App\Controller\Site;

use App\Service\PanierService;
use App\Service\AdresseService;
use App\Service\OccasionService;
use App\Repository\TaxRepository;
use App\Service\UtilitiesService;
use App\Repository\BoiteRepository;
use App\Repository\EditorRepository;
use App\Repository\AddressRepository;
use App\Repository\PartnerRepository;
use App\Repository\OccasionRepository;
use App\Form\SearchBoiteInCatalogueType;
use App\Form\SearchOccasionNameOrEditorInCatalogueType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SiteSettingRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Form\SearchOccasionsInCatalogueType;
use App\Repository\CollectionPointRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CatalogOccasionSearchRepository;
use App\Repository\DurationOfGameRepository;
use App\Service\CatalogueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

use function PHPUnit\Framework\throwException;

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
        private SiteSettingRepository $siteSettingRepository,
        private CatalogOccasionSearchRepository $catalogOccasionSearchRepository,
        private UtilitiesService $utilitiesService,
        private EntityManagerInterface $em,
        private Security $security,
        private CatalogueService $catalogueService,
        private DurationOfGameRepository $durationOfGameRepository,
        private RequestStack $requestStack
    )
    {
    }
    
     //TODO pièces détachées
    #[Route('/catalogue-pieces-detachees', name: 'app_catalogue_pieces_detachees')]
    public function cataloguePiecesDetachees(Request $request): Response
    {

        if($_ENV['APP_ENV'] == 'prod'){
            return $this->redirect($this->generateUrl('app_home') . '#piecesDetachees');
        }

        $siteSetting = $this->siteSettingRepository->findOneBy([]);

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


        $boites = $this->paginator->paginate(
            $donneesFromDatabases, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            24 /*limit per page*/
        );

        $partenaires = $this->partnerRepository->findBy(['isDisplayOnCatalogueWhenSearchIsNull' => true]);


        $metas['description'] = 'Catalogue complet de toutes les boites dont le service dispose de pièces détachées.';

        return $this->render('site/pages/catalog/pieces_detachees/pieces_detachees.html.twig', [
            'boites' => $boites,
            'form' => $form,
            'search' => $search ?? null,
            'partners' => $partenaires ?? null,
            'transforms' => $transforms ?? null,
            'metas' => $metas,
            'tax' => $this->taxRepository->findOneBy([]),
            'siteSetting' => $siteSetting
        ]);
    }

    //TODO pièces détachées
    #[Route('/catalogue-pieces-detachees/{id}/{editorSlug}/{boiteSlug}/', name: 'app_catalogue_pieces_detachees_articles_d_une_boite')]
    public function cataloguePiecesDetacheesArticlesDuneBoite($id, $editorSlug, $boiteSlug): Response
    {
        if($_ENV['APP_ENV'] == 'prod'){
            return $this->redirect($this->generateUrl('app_home') . '#piecesDetachees');
        }

        $boite = $this->boiteRepository->findOneBy(['id' => $id, 'slug' => $boiteSlug, 'editor' => $this->editorRepository->findOneBy(['slug' => $editorSlug]), 'isOnline' => true]);

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

        return $this->render('site/pages/catalog/pieces_detachees/articles_d_une_boite.html.twig', [
            'boite' => $boite,
            'metas' => $metas,
            'groups' => $groups,
            'tax' => $this->taxRepository->findOneBy([]),
        ]);
    }

    #[Route('/catalogue-jeux-occasion/{category}', name: 'app_catalogue_occasions')]
    public function catalogueOccasions(Request $request, $category = NULL): Response
    {

        $metas['description'] = "Plein de jeux de société d'occasion à petit prix ! Idéal pour les soirées entre amis ou en famille. Visitez nos catalogues et trouvez votre bonheur !";

        //values for form / query in repository etc...
        $choices = $this->occasionService->returnOptionsForFormAndTitleForOccasionCatalogByCategory($category);

        //génération du form avec les options
        $form = $this->createForm(SearchOccasionsInCatalogueType::class, null,
            [
                'method' => 'GET',
                'agesOptions' => $choices['ages_options_for_form'],
                'playersOptions' => $choices['players_options_for_form'],
                'durationsOptions' => $choices['durations_options_for_form'],
            ]);
        $form->handleRequest($request);

        //génération du form recherche name or editor
        $formNameOrEditor = $this->createForm(SearchOccasionNameOrEditorInCatalogueType::class, null, ['method' => 'GET',]);
        $formNameOrEditor->handleRequest($request);

        // if($form->isSubmitted() && $form->isValid()) {
        if($form->isSubmitted() OR $formNameOrEditor->isSubmitted() && $formNameOrEditor->isValid()) {

            $search = str_replace(" ","%", $formNameOrEditor->get('search')->getData()) ?? '';
            $ages = $form->get('ages')->getData() ?? [];
            $players = $form->get('players')->getData() ?? [];
            $durations = $form->get('durations')->getData() ?? [];

            //? on sauvegarde les 100 dernières recherche des utilisateurs
            // $this->catalogueService->saveSearchOccasionInDataBase($request, $phrase, $age_start, $players, $durations, 100);

            //?si on recherche un nom
            if($search != ''){

                //?si on cherche des jeux par nom ou par editeur on force retur au catalogue de tous les jeux par default
                $choices = $this->occasionService->returnOptionsForFormAndTitleForOccasionCatalogByCategory('tous-les-jeux');
                $donneesFromDatabases = $this->occasionRepository->findOccasionsInCatalogueByNameOrEditor($search, $choices);

            }else{

                //?si on cherche un seul nombre de joueurs
                if(count($players) == 1){

                    $donneesFromDatabasesPlayerMinAndPlayerMaxAsSame = $this->occasionRepository->findOccasionsInCatalogueWherePlayerMinAndPlayerMaxAreSame($players, $durations, $ages, $choices);
                    $donneesFromDatabasePlayersBetweenMinAndMax = $this->occasionRepository->findOccasionsInCatalogueWherePlayersBetweenMinAndMax($players, $durations, $ages, $choices);
                    //?on retire les doublons
                    $donneesFromDatabases = array_unique(array_merge($donneesFromDatabasesPlayerMinAndPlayerMaxAsSame,$donneesFromDatabasePlayersBetweenMinAndMax), SORT_REGULAR); //?on retire les doublons
                
                }else{

                    $donneesFromDatabases = $this->occasionRepository->searchOccasionsInCatalogue($ages, $players, $durations, $choices);
                    $donneesFromDatabasesPlayerMinAndPlayerMaxAsSame = $this->occasionRepository->findOccasionsInCatalogueWherePlayerMinAndPlayerMaxAreSame($players, $durations, $ages, $choices);

                    $donneesFromDatabases = array_unique(array_merge($donneesFromDatabases,$donneesFromDatabasesPlayerMinAndPlayerMaxAsSame), SORT_REGULAR); //?on retire les doublons

                }

            }


        }else{

            //on cherche l'ensemble du catalogue
            $donneesFromDatabases = $this->occasionRepository->searchOccasionsInCatalogue([], [], [], $choices);

        }
        // $diff = $this->catalogueService->returnOccasionsWithoutOccasionsInCart($donneesFromDatabases);


        $occasions = $this->paginator->paginate(
            $donneesFromDatabases, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            24, /*limit per page*/
        );

 
        //si url contient ajax et surtout ne contient pas page
        if($request->get('ajax') && !$request->get('page') && $search == '') {

            return new JsonResponse([
                'content' => $this->renderView('site/pages/catalog/components/_display_occasions_results.html.twig', [
                    'occasions' => $occasions,
                    'occasions_totales' => $occasions,
                    'metas' => $metas,
                    'titreDeLaPage' => $choices['twig']['titleH1'],
                    'breadcrumb' => $choices['twig']['breadcrumb'],
                    'form' => $form,
                    'formNameOrEditor' => $formNameOrEditor,
                    'tax' => $this->taxRepository->findOneBy([]),
                    'partners' => $this->partnerRepository->findBy(['isOnline' => true, 'isDisplayOnCatalogueWhenSearchIsNull' => true]),
                ])
            ]);

        }else{

            return $this->render('site/pages/catalog/occasions/les_occasions.html.twig', [
                'occasions' => $occasions,
                'occasions_totales' => $donneesFromDatabases,
                'metas' => $metas,
                'titreDeLaPage' => $choices['twig']['titleH1'],
                'breadcrumb' => $choices['twig']['breadcrumb'],
                'form' => $form,
                'formNameOrEditor' => $formNameOrEditor,
                'tax' => $this->taxRepository->findOneBy([]),
                'partners' => $this->partnerRepository->findBy(['isOnline' => true, 'isDisplayOnCatalogueWhenSearchIsNull' => true]),
            ]);

        }
    }

    #[Route('/jeu-occasion/{reference_occasion}/{editor_slug}/{boite_slug}', name: 'occasion')]
    public function occasion($reference_occasion, Security $security, $editor_slug, $boite_slug): Response
    {

        $delivery = null;
        $http_error_code = 200;

        $occasion = $this->occasionRepository->findUniqueOccasionByRefrenceV3WhenIsOnLineAndSlugsAreOk($reference_occasion, $editor_slug, $boite_slug);

        //gestion occasion entre v3 et v2
        if(!$occasion){
            $http_error_code = 301;
            $occasion = $this->occasionRepository->findUniqueOccasionByRefrenceV2WhenIsOnLineAndSlugsAreOk($reference_occasion, $editor_slug, $boite_slug);

            if(!$occasion){
                $http_error_code = 404;
                throw $this->createNotFoundException('Occasion non trouvée');
            }
        }

        $user = $security->getUser();

        if($user){

            $deliveryAdresse = $this->addressRepository->findOneBy(['user' => $user, 'isFacturation' => false]);

            if(is_null($deliveryAdresse)){

                $delivery = null;

            }else{

                $collectionPoint = $this->collectionPointRepository->findOneCollectionPointForOccasionBuy();

                $kmsBetweenCollectionPointAndDeliveryAdress = $this->adresseService->get_distance_from_collectePoint($collectionPoint, $deliveryAdresse);
                
                $setting = $this->siteSettingRepository->findOneBy([]);

                if($kmsBetweenCollectionPointAndDeliveryAdress < $setting->getDistanceMaxForOccasionBuy()){

                    $delivery = true;

                }else{

                    $delivery = false;

                }
            }

        }


        $query = $this->occasionRepository->findAleatoireOccasionsByAgeWhitoutThisOccasion($occasion->getBoite()->getAge(), $occasion);
        shuffle($query); // on mélange
        $firstElements = array_slice($query, 0, 4); //on prend les 6 premiers apres avoir mélanger
        $metas['description'] = 'Jeu d\'occasion vérifié, remis en état, et disponible à petit prix: '.strtolower(ucfirst($occasion->getBoite()->getName())).' - '.strtolower(ucfirst($occasion->getBoite()->getEditor()->getName()));

        return $this->render('site/pages/catalog/occasions/occasion.html.twig', [
            'occasion' => $occasion,
            'tax' => $this->taxRepository->findOneBy([]),
            'metas' => $metas,
            'delivery' => $delivery,
            'firstElements' => $firstElements
        ], new Response(null, $http_error_code));
    }
}

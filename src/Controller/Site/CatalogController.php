<?php

namespace App\Controller\Site;

use DateTimeImmutable;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    
    #[Route('/catalogue-pieces-detachees', name: 'app_catalogue_pieces_detachees')]
    public function cataloguePiecesDetachees(Request $request): Response
    {

        return $this->redirect($this->generateUrl('app_home') . '#piecesDetachees');

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

        //? effet rotation X ou Y aléatoire sur les cards
        $class_transforms = [['transformX','backX'],['transformY','backY']];
        for($i=0;$i<count($boites);$i++){
            shuffle($class_transforms);
            $transforms[] = $class_transforms[0];
        }

        $partenaires = $this->partnerRepository->findBy(['isDisplayOnCatalogueWhenSearchIsNull' => true]);


        $metas['description'] = 'Catalogue complet de toutes les boites dont le service dispose de pièces détachées.';

        return $this->render('site/pages/catalog/pieces_detachees/les_pieces_detachees.html.twig', [
            'boites' => $boites,
            'form' => $form,
            'search' => $search ?? null,
            'partners' => $partenaires ?? null,
            'transforms' => $transforms ?? null,
            'metas' => $metas,
            'tax' => $this->taxRepository->findOneBy([])

        ]);
    }

    //TODO pièces détachées
    #[Route('/catalogue-pieces-detachees/{editorSlug}/{id}/{slug}', name: 'catalogue_pieces_detachees_demande')]
    public function cataloguePiecesDetacheesDemande($id, $slug, $editorSlug, Request $request): Response
    {

        return $this->redirect($this->generateUrl('app_home') . '#piecesDetachees');

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

        return $this->render('site/pages/catalog/pieces_detachees/pieces_detachees_demande.html.twig', [
            'boite' => $boite,
            'metas' => $metas,
            'groups' => $groups,
            'tax' => $this->taxRepository->findOneBy([]),
        ]);
    }

    #[Route('/catalogue-jeux-occasion/{category}', name: 'app_catalogue_occasions')]
    public function catalogueOccasions(Request $request, $category = NULL): Response
    {

        $metas['description'] = 'Catalogue complet des jeux d\'occasion disponiblent en retrait sur Caen.';

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

            $search = $formNameOrEditor->get('search')->getData() ?? '';
            $phrase = str_replace(" ","%",$search);
            $age_start = $form->get('age_start')->getData() ?? [];
            $age_end = $choices['start_and_end_ages']['end'];
            $players = $form->get('playerMin')->getData() ?? [];
            $durations = $form->get('duration')->getData() ?? [];

            //TODO à montrer et activer si ok Antoine
            //$this->catalogueService->saveQueryInDataBase($request, $phrase, $age_start, $players, $durations);


            $donneesFromDatabases = $this->occasionRepository->searchOccasionsInCatalogue($phrase, $age_start, $age_end, $players, $durations, $choices);

        }else{

            //on cherche l'ensemble du catalogue
            $donneesFromDatabases = $this->occasionRepository->searchOccasionsInCatalogue('',[], $choices['start_and_end_ages']['end'],[],[],$choices);

        }
        
        $diff = $this->catalogueService->returnOccasionsWithoutOccasionsInCart($donneesFromDatabases);

        $occasions = $this->paginator->paginate(
            $diff, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            24, /*limit per page*/
        );
    
 
        //si url contient ajax et surtout ne contient pas page
        if($request->get('ajax') && !$request->get('page')) {

            return new JsonResponse([
                'content' => $this->renderView('site/pages/catalog/components/_display_occasions_results.html.twig', [
                    'occasions' => $occasions,
                    'occasions_totales' => $diff,
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
                'occasions_totales' => $diff,
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

        $occasion = $this->occasionRepository->findUniqueOccasionByRefrenceV3WhenIsOnLineAndSlugsAreOk($reference_occasion, $editor_slug, $boite_slug);

        //si toujours pas d'occasion => page non trouvée
        if(!$occasion){
            throw $this->createNotFoundException('Occasion non trouvé'); //TODO redirection vers page 'rangement'
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
        $metas['description'] = 'Jeu d\'occasion vérifié, remis en état, et disponible à petit prix: '.$occasion->getBoite()->getName().' - '.$occasion->getBoite()->getEditor()->getName();

        return $this->render('site/pages/catalog/occasions/occasion.html.twig', [
            'occasion' => $occasion,
            'tax' => $this->taxRepository->findOneBy([]),
            'metas' => $metas,
            'delivery' => $delivery,
            'firstElements' => $firstElements
        ]);
    }
}

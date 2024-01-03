<?php

namespace App\Controller\Site;

use App\Form\ItemChoiceType;
use App\Form\ItemsForm;
use App\Form\SearchBoiteInCatalogueType;
use App\Repository\BoiteRepository;
use App\Repository\EditorRepository;
use App\Repository\OccasionRepository;
use App\Repository\PartnerRepository;
use App\Repository\TaxRepository;
use App\Service\PanierService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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

            $donnees = $this->boiteRepository->findBoitesFromSearch($phrase);

        }else{

            $donnees = $this->boiteRepository->findBy(['isOnline' => true],['id' => 'DESC']);
        }

        $partenaires = $this->partnerRepository->findBy(['isDisplayOnCatalogueWhenSearchIsNull' => true]);
        
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

        return $this->render('site/catalog/pieces_detachees/les_pieces_detachees.html.twig', [
            'boites' => $boites,
            'boites_totales' => $donnees,
            'form' => $form,
            'search' => $search ?? null,
            'partenaires' => $partenaires ?? null,
            'transforms' => $transforms
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

        return $this->render('site/catalog/pieces_detachees/pieces_detachees_demande.html.twig', [
            'boite' => $boite
        ]);
    }

    #[Route('/catalogue-jeux-occasion', name: 'app_catalogue_occasions')]
    public function catalogueOccasions(Request $request): Response
    {
        $donnees = $this->occasionRepository->findBy(['isOnline' => true],['id' => 'DESC']);

        $occasions = $this->paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            24 /*limit per page*/
        );

        //? effet rotation X ou Y aléatoire sur les cards
        $class_transforms = [['transformX','backX'],['transformY','backY']];
        for($i=0;$i<count($occasions);$i++){
            shuffle($class_transforms);
            $transforms[] = $class_transforms[0];
        }

        return $this->render('site/catalog/occasions/les_occasions.html.twig', [
            'occasions' => $occasions,
            'occasions_totales' => $donnees,
            'transforms' => $transforms
        ]);
    }

    #[Route('/jeu-occasion/{reference_occasion}/{editor_slug}/{boite_slug}', name: 'occasion')]
    public function occasion($reference_occasion, $editor_slug): Response
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

        return $this->render('site/catalog/occasions/occasion.html.twig', [
            'occasion' => $occasion,
            'tax' => $this->taxRepository->findOneBy([])
        ]);
    }
}

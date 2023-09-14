<?php

namespace App\Controller\Site;

use App\Repository\BoiteRepository;
use App\Repository\EditorRepository;
use App\Repository\OccasionRepository;
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
        private EditorRepository $editorRepository
    )
    {
    }
    
    #[Route('/catalogue-pieces-detachees', name: 'app_catalogue_pieces_detachees')]
    public function cataloguePiecesDetachees(Request $request): Response
    {
        $donnees = $this->boiteRepository->findBy(['isOnline' => true],['id' => 'DESC']);

        $boites = $this->paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            24 /*limit per page*/
        );

        return $this->render('site/catalog/pieces_detachees/les_pieces_detachees.html.twig', [
            'boites' => $boites,
        ]);
    }


    #[Route('/catalogue-pieces-detachees/demande-boite/{id}/{slug}/{editor}', name: 'app_catalogue_pieces_detachees_demande')]
    public function cataloguePiecesDetacheesDemande($id, $slug, $editor): Response
    {

        $boite = $this->boiteRepository->findOneBy(['id' => $id, 'slug' => $slug, 'editor' => $this->editorRepository->findOneBy(['name' => $editor]), 'isOnline' => true]);

        if(!$boite){
            $this->addFlash('warning', 'Boite inconnue');
            return $this->redirectToRoute('app_catalogue_pieces_detachees');
        }

        return $this->render('site/catalog/pieces_detachees_demande.html.twig', [
            'boite' => $boite,
        ]);
    }

    #[Route('/catalogue-jeux-occasion', name: 'app_catalogue_occasions')]
    public function catalogueOccasions(Request $request): Response
    {
        $donnees = $this->occasionRepository->findBy(['isOnline' => true]);

        $occasions = $this->paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );

        return $this->render('site/catalog/occasions/les_occasions.html.twig', [
            'occasions' => $occasions,
        ]);
    }

    #[Route('/jeu-occasion/{reference_occasion}/{editor_slug}/{boite_slug}', name: 'app_occasion')]
    public function occasion($reference_occasion, $editor_slug): Response
    {

        $occasion = $this->occasionRepository->findOneBy(
            [
                'isOnline' => true,
                'reference' => $reference_occasion
            ]);

        if(!$occasion){

            //TODO flash message not view
            $this->addFlash('warning', 'Jeux non disponible ou inconnu !');
            return $this->redirectToRoute('app_catalogue_occasions');
        }

        return $this->render('site/catalog/occasions/occasion.html.twig', [
            'occasion' => $occasion,
        ]);
    }
}

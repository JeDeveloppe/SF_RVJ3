<?php

namespace App\Controller\Site;

use App\Repository\BoiteRepository;
use App\Repository\EditorRepository;
use App\Repository\OccasionRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SitemapController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger,
        private RouterInterface $routerInterface,
        private BoiteRepository $boiteRepository,
        private OccasionRepository $occasionRepository,
        private EditorRepository $editorRepository
        )
    {
    }

    #[Route('/sitemap.xml', name: 'site_sitemap_xml')]
    public function sitemapXml(Request $request): Response
    {

        //tableau vide
        $urls = [];
        $now = new DateTimeImmutable('now');
        $hostname = $request->getSchemeAndHttpHost();

        $collection = $this->routerInterface->getRouteCollection();
        $allRoutes = $collection->all();

        foreach($allRoutes as $key => $route){
            //! important toutes les routes pour le sitemap doivent commencer par app_ sauf les catalogues traités après
            if(substr($key,0,4) == 'app_'){
                //? on met dans le tableau les différentes route
                $urls[] = [
                    'loc'        => $this->generateUrl($key),
                    'lastmod'    => $now->format('Y-m-d'),
                    'changefreq' => "monthly", //monthly,daily
                    'priority'   => 0.8
                    ];
            }
        }      
        //! traitement des catalogues
        //?liste des occasions
        $occasions = $this->occasionRepository->findBy(['isOnline' => true]);

        foreach($occasions as $occasion){
            $urls[] = [                
                'loc'        => $this->generateUrl('occasion', ['reference_occasion' => $occasion->getReference(), 'editor_slug' => $occasion->getBoite()->getEditor()->getSlug() ?? "VIDE", 'boite_slug' => strtolower($occasion->getBoite()->getSlug() ?? "VIDE") ]),
                'lastmod'    => $occasion->getBoite()->getCreatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
                'priority'   => 0.8
            ];
        }

        //?liste des boites pour articles comme (v3)
        $boites = $this->boiteRepository->findBoitesWhereThereIsItems();

        foreach($boites as $boite){
            $urls[] = [                
                'loc'        => $this->generateUrl('catalogue_pieces_detachees_articles_d_une_boite', ['id' => $boite->getId(), 'boiteSlug' => strtolower($boite->getSlug()), 'editorSlug' => strtolower($boite->getEditor()->getSlug())]),
                'lastmod'    => $occasion->getBoite()->getCreatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
                'priority'   => 0.8
            ];
        }

        //! pour un catalogue
        //$listes = $this->repository->findAll();
        // foreach($listes as $item){
        //     $urls[] = [                
        //         'loc'     => $this->generateUrl('###'),
        //         'lastmod' => $item->getCreatedAt()->format('Y-m-d'),
        //         'changefreq' => "monthly",
        //         'priority' => 0.8
        //     ];
        // }

        $response = new Response(
            $this->renderView('site/sitemap/sitemap.html.twig', [
                'urls'     => $urls,
                'hostname' => $hostname
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/xml');
        
        return $response;
    }

    #[Route('/sitemap', name: 'site_sitemap')]
    public function index(Request $request): Response
    {

        return $this->redirectToRoute('site_sitemap_xml');
    }
}

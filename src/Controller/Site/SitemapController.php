<?php

namespace App\Controller\Site;

use App\Repository\BoiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger,
        private BoiteRepository $boiteRepository
        )
    {
    }

    #[Route('/sitemap', name: 'app_sitemap')]
    public function index(Request $request): Response
    {
        $hostname = $request->getSchemeAndHttpHost();
        $boites = $this->boiteRepository->findBy(['isOnline' => true],['id' => 'DESC']);
        
        //tableau vide
        $urls = [];

        //!liste des urls directes à completer OK
        $urls[] = [
                'loc'        => $this->generateUrl('app_home'),
                'changefreq' => "monthly", //monthly,daily
                'priority'   => 0.8
                ];
        $urls[] = [
            'loc'        => $this->generateUrl('app_conditions_generale_de_vente'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('app_mentions_legales'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('app_register'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('app_contact'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('app_login'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('app_register'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('app_partenaires'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];

        //! liste des urls des pieces detachee OK
        $urls[] = [
            'loc'        => $this->generateUrl('app_catalogue_pieces_detachees'),
            'changefreq' => "monthly",
            'priority'   => 0.8
            ];
        foreach($boites as $boite){
            $urls[] = [                
                'loc'     => $this->generateUrl('app_catalogue_pieces_detachees_demande', ['id' => $boite->getId(), 'slug' => $boite->getSlug(), 'editorSlug' => $boite->getEditor()->getSlug() ?? "VIDE" ]),
                'lastmod' => $boite->getCreatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
                'priority' => 0.8
            ];
        }

        //TODO et mettre au dessus
        // $urls[] = [
        //     'loc'        => $this->generateUrl('informations-comment-ca-marche'),
        //     'changefreq' => "monthly", //monthly,daily
        //     'priority'   => 0.8
        //     ];
        // $urls[] = [
        //     'loc'        => $this->generateUrl('nous-soutenir'),
        //     'changefreq' => "monthly", //monthly,daily
        //     'priority'   => 0.8
        //     ];


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
}
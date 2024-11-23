<?php

namespace App\Service;

use DateTimeImmutable;
use App\Entity\CatalogOccasionSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CatalogOccasionSearchRepository;
use App\Repository\OccasionRepository;
use App\Repository\PanierRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\RequestStack;

class CatalogueService
{
    public function __construct(
        private UtilitiesService $utilitiesService,
        private Security $security,
        private EntityManagerInterface $em,
        private CatalogOccasionSearchRepository $catalogOccasionSearchRepository,
        private OccasionRepository $occasionRepository,
        private RequestStack $requestStack,
        private PanierRepository $panierRepository
        ){
    }

    public function saveSearchOccasionInDataBase(Request $request, string $phrase, array $age, array $players, array $durations, int $requestMaxToSave)
    {
        if(!$request->query->getInt('page')){
            $data = new CatalogOccasionSearch();
            $data->setPhrase($phrase)
                    ->setAges($age)
                    ->setPlayers($players)
                    ->setDurations($durations)
                    ->setCreatedAt(new DateTimeImmutable('now'));
            $this->em->persist($data);
            $this->em->flush($data);
            $catalogueOccasionSearchs = $this->catalogOccasionSearchRepository->findAll();
            if(count($catalogueOccasionSearchs) > $requestMaxToSave){ //on garde les X dernières recherches
                $this->em->remove($catalogueOccasionSearchs[0]);
                $this->em->flush();
            }
        }
    }

    public function returnOccasionsWithoutOccasionsInCart(array $donneesFromDatabases)
    {
   
            $session = $this->requestStack->getSession();
            $paniersInSession = $session->get('paniers');
     
            $occasions_from_panier = [];
            foreach($paniersInSession['occasions'] as $key => $panierInSession){
                $occasions_from_panier[] = $this->occasionRepository->findOneBy(['id' => $key]);
            }

        return array_diff($donneesFromDatabases, $occasions_from_panier);
    }
}
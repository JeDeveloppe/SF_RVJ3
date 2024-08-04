<?php

namespace App\Service;

use DateTimeImmutable;
use App\Entity\CatalogOccasionSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CatalogOccasionSearchRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Console\Style\SymfonyStyle;

class CatalogueService
{
    public function __construct(
        private UtilitiesService $utilitiesService,
        private Security $security,
        private EntityManagerInterface $em,
        private CatalogOccasionSearchRepository $catalogOccasionSearchRepository
        ){
    }

    public function saveQueryInDataBase(Request $request, string $phrase, int $age, array $players)
    {
        if(!$request->query->getInt('page')){
            $requestMaxToSave = 100; //TODO Antoine
            $data = new CatalogOccasionSearch();
            $data->setPhrase($phrase)
                    ->setToken($this->utilitiesService->generateRandomString())
                    ->setAge($age)
                    ->setPlayers($players)
                    ->setCreatedAt(new DateTimeImmutable('now'))
                    ->setUser($this->security->getUser());
            $this->em->persist($data);
            $this->em->flush($data);
            $catalogueOccasionSearchs = $this->catalogOccasionSearchRepository->findAll();
            if(count($catalogueOccasionSearchs) > $requestMaxToSave){ //on garde les X dernières recherches
                $this->em->remove($catalogueOccasionSearchs[0]);
                $this->em->flush();
            }
        }
    }
}
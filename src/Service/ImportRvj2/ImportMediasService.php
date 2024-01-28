<?php

namespace App\Service\ImportRvj2;

use App\Entity\BadgeForMediaTimeline;
use App\Entity\Media;
use App\Entity\Partenaire;
use App\Entity\Partner;
use App\Repository\BadgeForMediaTimelineRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\MediaRepository;
use League\Csv\Reader;
use App\Repository\PartenaireRepository;
use App\Repository\PartnerRepository;
use App\Repository\PaysRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use App\Service\UtilitiesService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportMediasService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MediaRepository $mediaRepository,
        private UtilitiesService $utilitiesService,
        private BadgeForMediaTimelineRepository $badgeForMediaTimelineRepository,
        private UserRepository $userRepository
        ){
    }

    public function importMedias(SymfonyStyle $io): void
    {
        $io->title('Importation des medias');

        $medias = $this->readCsvFileMedias();
        
        $io->progressStart(count($medias));

        foreach($medias as $arrayMedia){
            $io->progressAdvance();
            $media = $this->createOrUpdateMedia($arrayMedia);
            $this->em->persist($media);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');

    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileMedias(): Reader
    {
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/_table_medias.csv','r');
        $csvCatalogue->setHeaderOffset(0);

        return $csvCatalogue;
    }

    private function createOrUpdateMedia(array $arrayMedia): Media
    {
        $media = $this->mediaRepository->findOneBy(['link' => $arrayMedia['lien']]);

        if(!$media){
            $media = new Media();
        }

        $badge = $this->badgeForMediaTimelineRepository->findOneBy(['name' => 'Radio']);
        $user = $this->userRepository->findOneBy(['email' => $_ENV['ADMIN_EMAIL']]);
    
        $media
            ->setTitle($arrayMedia['titre'])
            ->setDescription($arrayMedia['content'])
            ->setBadge($badge)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setCreatedBy($user)
            ->setIsOnLine(true)
            ->setLink($arrayMedia['lien'])
            ->setPublishedAt($this->utilitiesService->getDateTimeImmutableFromTimestamp($arrayMedia['date']));

        return $media;

    }

    public function addBadgeForMedia(){

        $badges = [];

        $badges[] = ['name' => 'Radio', 'text' => '	fa-solid fa-radio', 'bgcolor' => '#d3df2a'];

        foreach($badges as $badge){

            $badgeForMedia = $this->badgeForMediaTimelineRepository->findOneBy(['name' => $badge['name']]);

            if(!$badgeForMedia){
                $badgeForMedia = new BadgeForMediaTimeline();
            }

            $badgeForMedia
                ->setName($badge['name'])
                ->setText($badge['text'])
                ->setBgcolor($badge['bgcolor']);

            $this->em->persist($badgeForMedia);

        }
        $this->em->flush();

    }
}
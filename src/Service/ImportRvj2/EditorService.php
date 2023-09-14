<?php

namespace App\Service\ImportRvj2;

use App\Entity\Country;
use App\Entity\Editor;
use App\Repository\BoiteRepository;
use App\Repository\CountryRepository;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

class EditorService
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private EditorRepository $editorRepository,
        private EntityManagerInterface $em,
        private SluggerInterface $sluggerInterface
        ){
    }

    public function addEditorsInDatabase(SymfonyStyle $io): void
    {

        $io->title('Importation / mise à jour des éditeurs');

        $distinctEditors = $this->boiteRepository->findDistinctEditors();

        $io->progressStart(count($distinctEditors));

        foreach($distinctEditors as $simpleEditor){

            $editor = $this->editorRepository->findOneBy(['name' => $simpleEditor['initeditor']]);

            if(!$editor){
                $editor = new Editor();
            }

            $editor->setName($simpleEditor['initeditor'])->setSlug($this->sluggerInterface->slug(strtolower($simpleEditor['initeditor'])));

            $io->progressAdvance();
            $this->em->persist($editor);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Terminé !');

        $io->title('Update Entity->Editor in Boite');

        $boites = $this->boiteRepository->findAll();
        $io->progressStart(count($boites));


        foreach($boites as $boite){

            $editor = $this->editorRepository->findOneBy(['name' => $boite->getIniteditor()]);

            $boite->setEditor($editor);

            $io->progressAdvance();

            $this->em->persist($boite);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Terminé !');
    }
}
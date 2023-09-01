<?php

namespace App\Command;

use App\Service\ImportRvj2\CreationCountrieService;
use App\Service\ImportRvj2\EditorService;
use App\Service\ImportRvj2\ImportAdressesService;
use App\Service\ImportRvj2\ImportBoitesService;
use App\Service\ImportRvj2\ImportClientsService;
use App\Service\ImportRvj2\ImportDepartementsService;
use App\Service\ImportRvj2\ImportPartenairesService;
use App\Service\ImportRvj2\ImportPiecesService;
use App\Service\ImportRvj2\ImportVillesService;
use App\Service\SpaceViewsService;
use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'app:initforprod')]

class InitForProd extends Command
{
    public function __construct(
            private UserService $userService,
            private CreationCountrieService $creationCountrieService,
            private ImportClientsService $importClientsService,
            private ImportDepartementsService $importDepartementsService,
            private ImportVillesService $importVillesService,
            private ImportPartenairesService $importPartenairesService,
            private ImportAdressesService $importAdressesService,
            private ImportBoitesService $importBoitesService,
            private ImportPiecesService $importPiecesService,
            private EditorService $editorService
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        ini_set('memory_limit', '2048M');

        $io = new SymfonyStyle($input,$output);
        
        // creation PAYS name/isocode
        // $this->creationCountrieService->addCountries();
        
        //ON CREE OU ON MET A JOUR L'ADMIN
        // $this->userService->initForProd_adminUser($io);

        // on importe les clients
        //$this->importClientsService->importClients($io);

        //on importe les departementss
        // $this->importDepartementsService->importDepartements($io);

        //on importe les villes
        //$this->importVillesService->importVilles1_5($io);

        //on importe les partenaires
        //$this->importPartenairesService->importPartenaires($io);

        //on importe les adresses (facturation et livraison)
        // $this->importAdressesService->importAdresses($io);

        //on importe les boites
        //$this->importBoitesService->importBoites($io);

        //on genere les editeurs de facon distinct
        // $this->editorService->addEditorsInDatabase($io);

        //on importe le detail des boites
        //$this->importPiecesService->importPieces($io);

        return Command::SUCCESS;
    }

}
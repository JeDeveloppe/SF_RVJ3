<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use App\Service\ImportRvj2\ImportClientsService;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Service\ImportRvj2\CreationCountrieService;
use Symfony\Component\Console\Input\InputInterface;
use App\Service\ImportRvj2\ImportDepartementsService;
use App\Service\ImportRvj2\ImportVillesBelgesService;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\ImportRvj2\ImportVillesFrancaisesService;

#[AsCommand(name: 'app:initforprod1')]

class InitForProd1 extends Command
{
    public function __construct(
            private UserService $userService,
            private CreationCountrieService $creationCountrieService,
            private ImportClientsService $importClientsService,
            private ImportDepartementsService $importDepartementsService,
            private ImportVillesFrancaisesService $importVillesFrancaiseService,
            private ImportVillesBelgesService $importVillesBelgesService,
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        // ini_set('memory_limit', '2048M');
        ini_set("memory_limit", -1);

        $io = new SymfonyStyle($input,$output);
        
        // creation PAYS name/isocode
        $this->creationCountrieService->addCountries();
        
        //ON CREE OU ON MET A JOUR L'ADMIN
        $this->userService->initForProd_adminUser($io);

        // on importe les clients
        $this->importClientsService->importClients($io);

        //on importe les departementss
        $this->importDepartementsService->importDepartements($io);

        //on importe les villes francaises
        $this->importVillesFrancaiseService->importVilles1_5($io);
        $this->importVillesBelgesService->importVilles1_5($io);

        return Command::SUCCESS;
    }

}
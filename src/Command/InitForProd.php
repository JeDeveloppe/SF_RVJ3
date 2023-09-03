<?php

namespace App\Command;

use App\Service\ImportRvj2\CreationConditionOccasionService;
use App\Service\ImportRvj2\CreationCountrieService;
use App\Service\ImportRvj2\CreationLegalInformationService;
use App\Service\ImportRvj2\CreationMouvementsOccasionService;
use App\Service\ImportRvj2\CreationMoyenDePaiementService;
use App\Service\ImportRvj2\CreationUndefinedAdminAndAdresseService;
use App\Service\ImportRvj2\EditorService;
use App\Service\ImportRvj2\ImportAdressesService;
use App\Service\ImportRvj2\ImportBoitesService;
use App\Service\ImportRvj2\ImportClientsService;
use App\Service\ImportRvj2\ImportDepartementsService;
use App\Service\ImportRvj2\ImportDocumentsService;
use App\Service\ImportRvj2\ImportOccasionsService;
use App\Service\ImportRvj2\ImportPartenairesService;
use App\Service\ImportRvj2\ImportPiecesService;
use App\Service\ImportRvj2\ImportVillesService;
use App\Service\ImportRvj2\UpdateOccasionMouvement;
use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
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
            private EditorService $editorService,
            private CreationConditionOccasionService $creationConditionOccasionService,
            private CreationMoyenDePaiementService $creationMoyenDePaiementService,
            private ImportOccasionsService $importOccasionsService,
            private CreationMouvementsOccasionService $creationMouvementsOccasionService,
            private UpdateOccasionMouvement $updateOccasionMouvement,
            private ImportDocumentsService $importDocumentsService,
            private CreationLegalInformationService $creationLegalInformationService,
            private CreationUndefinedAdminAndAdresseService $creationUndefinedAdminAndAdresseService
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

        //on cree les conditions des occasions
        //$this->creationConditionOccasionService->addConditions($io);

        //on cree les MOYENS DE PAIEMENT
        //$this->creationMoyenDePaiementService->addMoyens($io);

        //on importe les jeux complet
        //$this->importOccasionsService->importOccasions($io);

        //on cree les mouvements des occasions
        //$this->creationMouvementsOccasionService->importMouvementsOccasions($io);

        //on met a jour les occasions avec les mouvements
        //$this->updateOccasionMouvement->updateOccasionMouvement($io);

        //on crer les information legale et la tax
        //$this->creationLegalInformationService->creationLegalInformation($io);

        //on cree undefined admin and is adresse for undefined user in document
        $this->creationUndefinedAdminAndAdresseService->creationAdminAdresse($io);

        //on importe les documents
        //$this->importDocumentsService->importDocuments($io);
        // $this->importPaiementService->importPaiements($io);

        return Command::SUCCESS;
    }

}
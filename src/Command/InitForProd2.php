<?php

namespace App\Command;

use App\Service\ImportRvj2\EditorService;
use Symfony\Component\Console\Command\Command;
use App\Service\ImportRvj2\ImportBoitesService;
use App\Service\ImportRvj2\ImportPiecesService;
use App\Service\ImportRvj2\ImportAdressesService;
use App\Service\ImportRvj2\ImportPaiementService;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\ImportRvj2\ImportDocumentsService;
use App\Service\ImportRvj2\ImportOccasionsService;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Service\ImportRvj2\UpdateOccasionMouvement;
use Symfony\Component\Console\Input\InputInterface;
use App\Service\ImportRvj2\ImportPartenairesService;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\ImportRvj2\ImportDocumentsLignesService;
use App\Service\ImportRvj2\CreationDocumentStatusService;
use App\Service\ImportRvj2\CreationMoyenDePaiementService;
use App\Service\ImportRvj2\CreationNombreDeJoueursService;
use App\Service\ImportRvj2\CreationLegalInformationService;
use App\Service\ImportRvj2\CreationConditionOccasionService;
use App\Service\ImportRvj2\CreationMouvementsOccasionService;
use App\Service\ImportRvj2\CreationUndefinedAdminAndAdresseService;
use App\Service\ImportRvj2\CreationEnvelopesAndColorsAndDiscountsService;
use App\Service\ImportRvj2\ImportMediasService;
use App\Service\ImportRvj2\SiteSettingsService;

#[AsCommand(name: 'app:initforprod2')]

class InitForProd2 extends Command
{
    public function __construct(
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
            private CreationUndefinedAdminAndAdresseService $creationUndefinedAdminAndAdresseService,
            private CreationDocumentStatusService $creationDocumentStatusService,
            private ImportPaiementService $importPaiementService,
            private ImportDocumentsLignesService $importDocumentsLignesService,
            private CreationEnvelopesAndColorsAndDiscountsService $creationEnvelopesAndColorsAndDiscountsService,
            private CreationNombreDeJoueursService $creationNombreDeJoueursService,
            private SiteSettingsService $siteSettingsService,
            private ImportMediasService $importMediasService
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        // ini_set('memory_limit', '2048M');
        ini_set("memory_limit", -1);

        $io = new SymfonyStyle($input,$output);

        // //on importe les partenaires
        // $this->importPartenairesService->importPartenaires($io);

        // //on importe les adresses (facturation et livraison)
        // $this->importAdressesService->importAdresses($io);

        // //on creer le nombre de joueurs
        // $this->creationNombreDeJoueursService->addplayers($io);

        // //on importe les boites
        // $this->importBoitesService->importBoites($io);

        // //on genere les editeurs de facon distinct
        // $this->editorService->addEditorsInDatabase($io);

        // //on importe le detail des boites
        // $this->importPiecesService->importPieces($io);

        // // //on cree utilisateur undefini, adresse de retrait COOP, methodes de retrait
        // $this->creationUndefinedAdminAndAdresseService->creationAdminAdresseAndShippingMethod($io);

        // //on cree les conditions des occasions
        // $this->creationConditionOccasionService->addConditions($io);

        // //on cree les MOYENS DE PAIEMENT
        // $this->creationMoyenDePaiementService->addMoyens($io);

        // //on importe les jeux complet
        // $this->importOccasionsService->importOccasions($io);

        // //on cree les mouvements des occasions
        // $this->creationMouvementsOccasionService->importMouvementsOccasions($io);

        // //on met a jour les occasions avec les mouvements
        // $this->updateOccasionMouvement->updateOccasionMouvement($io);

        // //on crer les information legale et la tax
        // $this->creationLegalInformationService->creationLegalInformation($io);

        // //on cree les status des documents
        // $this->creationDocumentStatusService->creationStatus($io);

        // //on importe les documents et les paiements
        // $this->importDocumentsService->importDocuments($io);
        // $this->importPaiementService->importPaiements($io);
        // $this->importDocumentsService->creationDocumentSending($io);

        // //on importe les lignes de chaque document
        // $this->importDocumentsLignesService->importDocumentsLigneBoites($io);
        // $this->importDocumentsLignesService->importDocumentsLigneOccasion($io);
        // $this->importDocumentsLignesService->generateDocumentsTotals($io);

        //on cree les enveloppes et les couleurs pour les articles, les enveloppes, les joueurs, les livraisons
        // $this->creationEnvelopesAndColorsAndDiscountsService->addDelivery();
        // $this->creationEnvelopesAndColorsAndDiscountsService->addEnvelopes($io);
        // $this->creationEnvelopesAndColorsAndDiscountsService->addColors($io);
        // $this->creationEnvelopesAndColorsAndDiscountsService->addDiscounts($io);

        //on cree les settings du site
        // $this->siteSettingsService->addSettings($io);

        //on injecte les medias
        $this->importMediasService->addBadgeForMedia();
        $this->importMediasService->importMedias($io);


        return Command::SUCCESS;
    }

}
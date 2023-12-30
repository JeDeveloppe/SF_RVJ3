<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Item;
use DateTimeImmutable;
use App\Service\ItemService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function __construct(
        private Security $security,
        private ItemService $itemService
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Général'),
            TextField::new('reference')->setLabel('Référence: (construite à partir de la première boite)')->setDisabled(true)->setColumns(6),
            IdField::new('id')->setLabel('Id')->setDisabled(true)->onlyOnForms()->setColumns(6),
            TextField::new('name')
                ->setLabel('Nom:')->setColumns(6),
            AssociationField::new('itemGroup')
                ->setLabel('Groupe d\'articles:')->onlyOnForms(),
            AssociationField::new('BoiteOrigine')
                ->setLabel('Boites Originale:')->onlyOnForms()->setColumns(6),
            AssociationField::new('BoiteSecondaire')
                ->setLabel('Boites Secondaire:')->setDisabled(true)->onlyOnForms()->setColumns(6),
            IntegerField::new('stockForSale')
                ->setLabel('Stock à la vente:')->setColumns(6),
            IntegerField::new('priceExcludingTax')
                ->setLabel('Prix unitaire HT (en cents):')->onlyOnForms()->setColumns(6),
            IntegerField::new('weigth')
                ->setLabel('Poid (en gramme):')->onlyOnForms()->setColumns(6),
            AssociationField::new('Envelope')->setLabel('Enveloppe:')
            ->setFormTypeOptions(['placeholder' => 'Sélectionner une enveloppe...'])->onlyOnForms()->setColumns(6),
            ArrayField::new('BoiteOrigine')
            ->setLabel('Boites Originale:')->onlyOnIndex(),
            FormField::addTab('Création / Mise à jour'),
            AssociationField::new('createdBy')->setDisabled(true)->onlyOnForms(),
            DateTimeField::new('createdAt')->setDisabled(true)->onlyOnForms()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des articles')
            ->setPageTitle('new', 'Nouvel article')
            ->setPageTitle('edit', 'Édition d\'un article')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'BoiteOrigine.name', 'BoiteSecondaire.id', 'BoiteSecondaire.name', 'reference', 'BoiteOrigine.id'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Item) {
            $user = $this->security->getUser();
            $entityInstance->setCreatedAt(new DateTimeImmutable ('now'))->setCreatedBy($user);

            $entityManager->persist($entityInstance);
            $entityManager->flush();

            //?on construit la référence à partir de la première boite
            $boites = $entityInstance->getBoiteOrigine();
            $reference = $this->itemService->creationReference($boites[0], $entityInstance);
            
            //?et on remet tout dans la bdd
            $entityInstance->setReference($reference);

            $entityManager->persist($entityInstance);
            $entityManager->flush();

        }
    }
}

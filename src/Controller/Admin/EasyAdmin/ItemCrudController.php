<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setLabel('Id')->setDisabled(true),
            AssociationField::new('itemGroup')
                ->setLabel('Groupe d\'articles:')->onlyOnForms(),
            AssociationField::new('BoiteOrigine')
                ->setLabel('Boites Originale:'),
            AssociationField::new('BoiteSecondaire')
                ->setLabel('Boites Secondaire:')->setDisabled(true),
            TextField::new('name')
                ->setLabel('Nom:'),
            IntegerField::new('stockForSale')
                ->setLabel('Stock à la vente:'),
            IntegerField::new('priceExcludingTax')
                ->setLabel('Prix unitaire HT (en cents):')->onlyOnForms(),
            IntegerField::new('weigth')
                ->setLabel('Poid (en gramme):')->onlyOnForms(),
            AssociationField::new('Envelope')->setLabel('Enveloppe:')
            ->setFormTypeOptions(['placeholder' => 'Sélectionner une enveloppe...'])->onlyOnForms()
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
            ->setSearchFields(['name', 'boite.name'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
        
    }
}

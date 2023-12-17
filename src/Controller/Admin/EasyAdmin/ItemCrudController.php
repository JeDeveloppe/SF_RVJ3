<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('itemGroup')
                ->setLabel('Groupe d\'articles:'),
            AssociationField::new('boite')
                ->setLabel('Boites rattachées:'),
            TextField::new('name')
                ->setLabel('Nom:'),
            IntegerField::new('stockForSale')
                ->setLabel('Stock à la vente:'),
            IntegerField::new('priceExcludingTax')
                ->setLabel('Prix unitaire HT (en cents):'),
            IntegerField::new('weigth')
                ->setLabel('Poid (en gramme):'),
            AssociationField::new('Envelope')->setLabel('Enveloppe:')
            ->setFormTypeOptions(['placeholder' => 'Sélectionner une enveloppe...'])
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
}

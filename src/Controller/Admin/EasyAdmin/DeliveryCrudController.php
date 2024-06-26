<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Delivery;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class DeliveryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Delivery::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('shippingMethod')->setLabel('Mode de livraison')->setFormTypeOptions(['placeholder' => 'Sélectionner...']),
            IntegerField::new('start')->setLabel('De (poid en gramme)'),
            IntegerField::new('end')->setLabel('A (poid en gramme (enveloppe comprise))'),
            IntegerField::new('priceExcludingTax')->setLabel('Prix hors taxe en cents')->onlyOnIndex(),
            IntegerField::new('priceExcludingTax')->setLabel('Prix hors taxe en cents (penser au prix de l\'enveloppe)')->onlyOnForms(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des prix par livraison')
            ->setPageTitle('new', 'Nouvelle livraison')
            ->setPageTitle('edit', 'Édition livraison')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
}

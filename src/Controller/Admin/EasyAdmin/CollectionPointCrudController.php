<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\CollectionPoint;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CollectionPointCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CollectionPoint::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom:'),
            TextField::new('street')->setLabel('Adresse:'),
            AssociationField::new('city')->setLabel('Ville:'),
            BooleanField::new('isActivedInCart')->setLabel('Actif dans le panier:')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Lieux de retrait')
            ->setPageTitle('new', 'Nouveau lieu de retrait')
            ->setPageTitle('edit', 'Édition lieu de retrait')
        ;
    }
}

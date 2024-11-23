<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\CollectionPoint;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
            AssociationField::new('shippingmethod')->setLabel('Envoi / retrait')->setFormTypeOptions(['placeholder' => 'Sélectionner la méthode concernée...']),
            AssociationField::new('documentStatus')->setLabel('Type de document')->setFormTypeOptions(['placeholder' => 'Sélectionner la méthode concernée...']),
            TextField::new('organization')->setLabel('Organisation:'),
            TextField::new('firstname')->setLabel('Nom:'),
            TextField::new('lastname')->setLabel('Prénom:'),
            TextField::new('street')->setLabel('Adresse:'),
            AssociationField::new('city')->setLabel('Ville:')->autocomplete(),
            TextField::new('timeToSend')->setLabel('Delais d\'envoi:'),
            BooleanField::new('isActivedInCart')->setLabel('Actif dans le panier:'),
            BooleanField::new('isOriginForWebSiteCmds')->setLabel('Sert d\'origine pour cmd web (distance):'),
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

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
}

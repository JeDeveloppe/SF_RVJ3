<?php

namespace App\Controller\Admin;

use App\Entity\Occasion;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;


class OccasionCrudController extends AbstractCrudController
{   
    public static function getEntityFqcn(): string
    {
        return Occasion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('boite')->setLabel('Boite'),
            TextField::new('reference')->setLabel('Référence'),
            TextField::new('information')->setLabel('Information sur l\'occasion'),
            BooleanField::new('isOnline')->setLabel('En ligne'),
            AssociationField::new('offSiteSale')->setLabel('Mouvement'),
            BooleanField::new('isNew')->setLabel('Neuf')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des occasions')
            ->setPageTitle('new', 'Nouvel occasion')
            ->setPageTitle('edit', 'Édition d\'un occasion')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN');
        
    }
}

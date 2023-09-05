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
            AssociationField::new('boite')->setLabel('Boite')->setDisabled(true),
            TextField::new('reference')->setLabel('Référence')->setDisabled(true),
            TextField::new('information')->setLabel('Information sur l\'occasion'),
            AssociationField::new('boxCondition')->setLabel('État de la boite'),
            AssociationField::new('equipmentCondition')->setLabel('État des pièces'),
            AssociationField::new('gameRule')->setLabel('Régle du jeu'),
            BooleanField::new('isOnline')->setLabel('En ligne'),
            AssociationField::new('offSiteSale')->setLabel('Vente / don')->setFormTypeOptions(['placeholder' => 'Sélectionner...']),
            BooleanField::new('isNew')->setLabel('Neuf')->onlyOnForms()->onlyOnForms()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des occasions')
            ->setPageTitle('new', 'Nouvel occasion')
            ->setPageTitle('edit', 'Édition d\'un occasion')
            ->setDefaultSort(['boite.name' => 'ASC'])
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

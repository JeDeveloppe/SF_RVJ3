<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\ConditionOccasion;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConditionOccasionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ConditionOccasion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            AssociationField::new('boxConditions')->setLabel('Les boites en...')->setDisabled(true),
            AssociationField::new('equipmentConditions')->setLabel('Les pièces en...')->setDisabled(true),
            AssociationField::new('gameRules')->setLabel('La règle du jeu en...')->setDisabled(true),
        ];
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des états')
            ->setPageTitle('new', 'Nouvel état')
            ->setPageTitle('edit', 'Édition d\'un état')

        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ->remove(Crud::PAGE_INDEX, Action::DELETE);
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
}

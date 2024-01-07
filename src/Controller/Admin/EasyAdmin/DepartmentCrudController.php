<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Department;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DepartmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Department::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('country')->setLabel('Pays')->setFormTypeOptions(['placeholder' => 'Sélectionner un pays...']),
            TextField::new('name')->setLabel('Numéro de département / nom'),
            AssociationField::new('cities')->setLabel('Nombre de villes')->onlyOnIndex()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des départements')
            ->setPageTitle('new', 'Nouveau département')
            ->setPageTitle('edit', 'Édition du département')
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
}

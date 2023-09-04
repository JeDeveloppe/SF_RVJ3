<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EditorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Editor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom'),
            AssociationField::new('boites')->setLabel('Nombre de boites')->onlyOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des éditeurs')
            ->setPageTitle('new', 'Nouvel éditeur')
            ->setPageTitle('edit', 'Édition d\'un éditeur')

        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
}

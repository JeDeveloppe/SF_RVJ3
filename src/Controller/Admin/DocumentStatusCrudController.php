<?php

namespace App\Controller\Admin;

use App\Entity\DocumentStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class DocumentStatusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DocumentStatus::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            AssociationField::new('documents')->setLabel('Documents')->setDisabled(true)
        ];
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des status des documents')
            ->setPageTitle('new', 'Nouveau status de document')
            ->setPageTitle('edit', 'Édition d\'un status de document')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ->remove(Crud::PAGE_INDEX, Action::DELETE);
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN');
        
    }
}

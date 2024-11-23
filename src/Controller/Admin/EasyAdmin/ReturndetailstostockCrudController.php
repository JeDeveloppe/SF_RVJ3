<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Returndetailstostock;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReturndetailstostockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Returndetailstostock::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('document')->setDisabled(true),
            TextareaField::new('question')->setDisabled(true),
            TextareaField::new('answer')->onlyOnForms()->setDisabled(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des devis supprimés avec pièces (comme V2)')
            ->setPageTitle('edit', 'Détail du document supprimé')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::DELETE)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN);
        
    }
}

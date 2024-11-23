<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Documentsending;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DocumentsendingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Documentsending::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('document'),
            DateTimeField::new('sendingAt'),
            TextField::new('sendingNumber'),
            AssociationField::new('shippingMethod')->renderAsEmbeddedForm(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des envois')
            ->setDefaultSort(['document.billNumber' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);
        
    }
}

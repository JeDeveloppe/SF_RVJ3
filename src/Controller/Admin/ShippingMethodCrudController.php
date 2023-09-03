<?php

namespace App\Controller\Admin;

use App\Entity\ShippingMethod;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShippingMethodCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ShippingMethod::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des moyens de retrait')
            ->setPageTitle('new', 'Nouveau retrait')
            ->setPageTitle('edit', 'Édition du retrait')
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

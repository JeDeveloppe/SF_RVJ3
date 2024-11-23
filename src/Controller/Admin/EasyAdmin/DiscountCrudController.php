<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Discount;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DiscountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Discount::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('start')->setLabel('De (nombre de pièces achetées)')->onlyOnForms(),
            IntegerField::new('end')->setLabel('A (nombre de pièces achetées)')->onlyOnForms(),
            TextField::new('fromTo','De ... à ... inclus')->onlyOnIndex()->setTextAlign('center'),
            IntegerField::new('value')->setLabel('Valeur de remise % (ex: 10)')->setTextAlign('center'),
            BooleanField::new('isOnline')->setLabel('Actif')->setTextAlign('center')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des remises')
            ->setPageTitle('new', 'Nouvelle remise')
            ->setPageTitle('edit', 'Édition remise')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
}

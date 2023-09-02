<?php

namespace App\Controller\Admin;

use App\Entity\OffSiteOccasionSale;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OffSiteOccasionSaleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OffSiteOccasionSale::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('movementTime')
                ->setLabel('Date de mouvement')
                ->setFormat('dd-MM-yyy à HH:mm' ),
            AssociationField::new('occasion'),
            MoneyField::new('movementPrice')
                ->setLabel('Prix du mouvement')
                ->setCurrency('EUR')->setStoredAsCents(),
            AssociationField::new('meansOfPaiement')
                ->setLabel('Moyen de paiement')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...']),
        ];
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des mouvements des occasions')
            ->setPageTitle('new', 'Nouveau mouvement d\'un occasion')
            ->setPageTitle('edit', 'Édition d\'un occasion')
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

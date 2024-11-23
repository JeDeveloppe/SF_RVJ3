<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\MeansOfPayement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class MeansOfPayementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MeansOfPayement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            AssociationField::new('payments')->setLabel('Paiements')->onlyOnIndex(),
            CollectionField::new('payments')->setLabel('Documents')->onlyOnForms()->setDisabled(true),

        ];
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des moyens de paiement')
            ->setPageTitle('new', 'Nouveau moyen de paiement')
            ->setPageTitle('edit', 'Édition d\'un moyen de paiement')
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
        
    }
}

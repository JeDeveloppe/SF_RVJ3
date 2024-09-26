<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\City;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return City::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            // AssociationField::new('country')
            //     ->setLabel('voir')
            //     ->renderAsEmbeddedForm(),
            TextField::new('name')->setLabel('Nom'),
            IntegerField::new('postalcode')->setLabel('Code postal'),
            TextField::new('latitude')->setLabel('Latitude')->onlyOnForms(),
            TextField::new('longitude')->setLabel('Longitude')->onlyOnForms(),
            AssociationField::new('addresses')->setLabel('Nbre d\'adresses')->onlyOnIndex(),
            AssociationField::new('partners')->setLabel('Nbre de partenaires')->onlyOnIndex(),
            AssociationField::new('department')
            ->setLabel('Département'),
            AssociationField::new('country'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPaginatorPageSize(50)
            ->setPageTitle('index', 'Liste des villes')
            ->setPageTitle('new', 'Nouvelle ville')
            ->setPageTitle('edit', 'Édition de la ville')
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

<?php

namespace App\Controller\Admin;

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
            AssociationField::new('department')->setLabel('Département')->setFormTypeOptions(['placeholder' => 'Sélectionner un département...']),
            TextField::new('name')->setLabel('Numéro / nom'),
            IntegerField::new('postalcode')->setLabel('Code postal'),
            TextField::new('latitude')->setLabel('Latitude'),
            TextField::new('longitude')->setLabel('Longitude'),
            AssociationField::new('addresses')->setLabel('Nombre d\'adresses')->onlyOnIndex(),
            AssociationField::new('partners')->setLabel('Nombre de partenaires')->onlyOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des villes')
            ->setPageTitle('new', 'Nouvelle ville')
            ->setPageTitle('edit', 'Édition de la ville')

        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ->remove(Crud::PAGE_INDEX, Action::DELETE);
            ->setPermission(Action::DELETE, 'ROLE_ADMIN');
        
    }
}

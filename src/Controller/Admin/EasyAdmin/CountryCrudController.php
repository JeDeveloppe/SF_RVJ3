<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Country;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;

class CountryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Country::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('isocode')->setLabel('Code ISO'),
            TextField::new('name')->setLabel('Nom'),
            BooleanField::new('isActifInInscriptionForm')->setLabel('Actif à \'inscription:'),
            // AssociationField::new('departments')->setLabel('Nombre de départements')->onlyOnIndex(),
            // AssociationField::new('cities')->setLabel('Nombre de villes')->onlyOnIndex(),
            AssociationField::new('users')->setLabel('Nombre de clients')->onlyOnIndex()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des pays')
            ->setPageTitle('new', 'Nouveau pays')
            ->setPageTitle('edit', 'Édition du pays')
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

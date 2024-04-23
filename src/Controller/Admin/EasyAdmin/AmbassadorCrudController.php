<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Ambassador;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AmbassadorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ambassador::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Informations affichées'),
            TextField::new('organization')->setLabel('Organisation:'),
            TextField::new('lastname')->setLabel('Nom:')->onlyOnForms()->setColumns(6),
            TextField::new('firstname')->setLabel('Prénom:')->onlyOnForms()->setColumns(6),
            TelephoneField::new('phone')->setLabel('Téléphone:')->onlyOnForms()->setColumns(6),
            EmailField::new('email')->setLabel('Adresse email:')->onlyOnForms()->setColumns(6),
            TextField::new('street')->setLabel('Adresse complète:')->onlyOnForms(),
            AssociationField::new('city')->setLabel('Ville:')->autocomplete()->onlyOnForms(),
            TextareaField::new('description')->setLabel('Description:')->onlyOnForms()->setColumns(6),
            UrlField::new('fullurl')->setLabel('Adresse url complète:')->onlyOnForms(),
            UrlField::new('facebookLink')->setLabel('Url Facebook:')->onlyOnForms(),
            UrlField::new('instagramLink')->setLabel('Url Instagram:')->onlyOnForms(),
            
            FormField::addTab('Informations privées'),
            TextField::new('privatelastname')->setLabel('Nom:')->setColumns(6),
            TextField::new('privatefirstname')->setLabel('Prénom:')->setColumns(6),
            TelephoneField::new('privatephone')->setLabel('Téléphone:')->onlyOnForms()->setColumns(6),
            EmailField::new('privateemail')->setLabel('Adresse email:')->onlyOnForms()->setColumns(6),
            TextField::new('privatestreet')->setLabel('Adresse complète:'),
            AssociationField::new('privatecity')->setLabel('Ville:')->autocomplete(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des ambassadeurs')
            ->setPageTitle('new', 'Nouvel ambassadeur')
            ->setPageTitle('edit', 'Édition d\'un ambassadeur')
            ->setDefaultSort(['lastname' => 'ASC'])
        ;
    }

}

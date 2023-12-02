<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {

        $choices = json_decode(ArrayField::new('roles'));
        dd($choices);

        return [

            FormField::addTab('Infos générales'),
            IdField::new('rvj2id')->setLabel('Rvj2Id')->setDisabled(true),
            ArrayField::new('roles')->setLabel('Roles'),
            TextField::new('email')->setLabel('Adresse email'),
            TextField::new('nickname')->setLabel('Pseudo (pour les admins)')->onlyOnForms()->setFormTypeOptions(['attr' => ['placeholder' => 'Uniquement pour un admin...']]),
            TelephoneField::new('phone')->setLabel('Téléphone')->onlyOnForms(),
            DateTimeField::new('createdAt')->setLabel('Date d\'inscription')->setFormat('dd.MM.yyyy')->setDisabled(true),
            DateTimeField::new('lastvisite')->setLabel('Dernière visite')->setFormat('dd.MM.yyyy')->setDisabled(true),
            DateTimeField::new('membership')->setLabel('Abonnement jusqu\'au')->setFormat('dd.MM.yyyy')->onlyOnForms()->setDisabled(true),

            FormField::addTab('Adresses'),
            AssociationField::new('addresses')->setLabel('Adresses')->onlyOnIndex(),
            AssociationField::new('addresses')->setLabel('Adresses')->onlyOnForms()->setDisabled(true),
            
            FormField::addTab('Achats'),
            AssociationField::new('documents')->setLabel('Documents')->onlyOnIndex(),
            AssociationField::new('documents')->setLabel('Documents')->onlyOnForms()->setDisabled(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des clients')
            ->setPageTitle('new', 'Nouveau client')
            ->setPageTitle('edit', 'Édition du client')

        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN');
        
    }
}

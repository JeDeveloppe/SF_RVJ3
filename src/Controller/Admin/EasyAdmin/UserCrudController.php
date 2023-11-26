<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('rvj2id')->setLabel('Rvj2Id')->setDisabled(true),
            TextField::new('email')->setLabel('Adresse email'),
            TextField::new('nickname')->setLabel('Pseudo')->onlyOnForms()->setFormTypeOptions(['attr' => ['placeholder' => 'Uniquement pour un admin...']]),
            TelephoneField::new('phone')->setLabel('Téléphone')->onlyOnForms(),
            DateTimeField::new('createdAt')->setLabel('Date d\'inscription')->setFormat('dd.MM.yyyy')->setDisabled(true),
            DateTimeField::new('lastvisite')->setLabel('Dernière visite')->setFormat('dd.MM.yyyy')->setDisabled(true),
            DateTimeField::new('membership')->setLabel('Abonnement jusqu\'au')->setFormat('dd.MM.yyyy')->onlyOnForms()->setDisabled(true),
            AssociationField::new('addresses')->setLabel('Adresses')->onlyOnIndex(),
            AssociationField::new('documents')->setLabel('Documents')->onlyOnIndex(),
            CollectionField::new('documents')->setLabel('Documents')->onlyOnForms()->setDisabled(true),
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
        $impersonate = Action::new('impersonate', false, 'fa fa-fw fa-user-lock')
        //changed from linkToRoute to linkToUrl. note that linkToUrl has only one parameter.
        //"admin/.. can be adjusted to another URL"
        ->linkToUrl(function (User $entity) {
            return 'admin/?_switch_user='.$entity->getEmail();
        })
        ;


        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $impersonate)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN');
        
    }
}

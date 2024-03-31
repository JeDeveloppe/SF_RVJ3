<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\NumbersOfPlayers;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class NumbersOfPlayersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NumbersOfPlayers::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('A partir de ... joueur(s)'),
            TextField::new('keyword')->setLabel('keyword dataBase (u1 et u2)')->setPermission('ROLE_SUPER_ADMIN'),
            AssociationField::new('boitesMin')->setLabel('Nombre de boites')->setDisabled(true)->onlyOnIndex(),
            AssociationField::new('boitesMax')->setLabel('Nombre de boites')->setDisabled(true)->onlyOnIndex()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des joueurs')
            ->setPageTitle('new', 'Nouveau joueur')
            ->setPageTitle('edit', 'Édition joueur')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
}

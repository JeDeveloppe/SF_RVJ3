<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\CatalogOccasionSearch;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CatalogOccasionSearchCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CatalogOccasionSearch::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('phrase','Recherche'),
            ArrayField::new('ages','A partir de (âge)'),
            ArrayField::new('players','Nbr de Joueurs mini'),
            ArrayField::new('durations','Temps de jeu'),
            DateTimeField::new('createdAt','Date')->setFormat('dd.MM.yyyy')->setTimezone('Europe/Paris')
        ];
    }

    public function configureActions(Actions $actions): Actions
    {

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);        
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('ages')
            ->add('players')
            ->add('durations')
            ->add('phrase')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des 100 dernières recherches')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['phrase', 'age']);
    }
}

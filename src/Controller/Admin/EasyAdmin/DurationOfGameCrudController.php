<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\DurationOfGame;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class DurationOfGameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DurationOfGame::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Texte affiché'),
            IntegerField::new('orderOfAppearance', 'Ordre affichage' )
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des durées des parties')
            ->setPageTitle('new', 'Nouvelle durée')
            ->setPageTitle('edit', 'Édition d\'une durée')
        ;
    }
  
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
        
    }
}

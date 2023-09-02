<?php

namespace App\Controller\Admin;

use App\Entity\MovementOccasion;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MovementOccasionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MovementOccasion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')
        ];
    }

    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des mouvements')
            ->setPageTitle('new', 'Nouveau mouvement')
            ->setPageTitle('edit', 'Édition d\un moouvement')

        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ->remove(Crud::PAGE_INDEX, Action::DELETE);
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
}

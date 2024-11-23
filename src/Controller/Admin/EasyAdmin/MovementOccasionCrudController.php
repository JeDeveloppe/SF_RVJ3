<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\MovementOccasion;
use DoctrineExtensions\Query\Mysql\Acos;
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
            ->setPageTitle('index', 'Mouvements des occasions')
            ->setPageTitle('new', 'Nouveau mouvement d\'occasion')
            ->setPageTitle('edit', 'Édition d\un mouvement')

        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
        
    }
}

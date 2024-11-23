<?php

namespace App\Controller\Admin\EasyAdmin;

use Doctrine\ORM\QueryBuilder;
use App\Entity\ConditionOccasion;
use phpDocumentor\Reflection\Types\Integer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ConditionOccasionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ConditionOccasion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            ColorField::new('color', 'Couleur'),
            IntegerField::new('discount', 'Remise sur le neuf<br/> (en cents)'),
            AssociationField::new('boxConditions')->setLabel('Les boites en...')->setDisabled(true)
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => 
                $queryBuilder
                ->where('entity.isOnline = :value')
                ->setParameter('value', true)
            )->onlyOnIndex(),
            AssociationField::new('equipmentConditions')->setLabel('Les pièces en...')->setDisabled(true)->onlyOnIndex(),
            AssociationField::new('gameRules')->setLabel('La règle du jeu en...')->setDisabled(true)->onlyOnIndex()
        ];
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des états (pièces,boite,règle)')
            ->setPageTitle('new', 'Nouvel état')
            ->setPageTitle('edit', 'Édition d\'un état')

        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ->remove(Crud::PAGE_INDEX, Action::DELETE);
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
}

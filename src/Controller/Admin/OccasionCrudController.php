<?php

namespace App\Controller\Admin;

use App\Entity\Occasion;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Component\HttpFoundation\RequestStack;

class OccasionCrudController extends AbstractCrudController
{   
    public static function getEntityFqcn(): string
    {
        return Occasion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('boite')->setLabel('Boite'),
            TextField::new('reference')->setLabel('Référence'),
            TextField::new('information')->setLabel('Information sur l\'occasion'),
            BooleanField::new('isOnline')->setLabel('En ligne'),
            AssociationField::new('movement')->setLabel('Mouvement (don / vente)')->setFormTypeOptions(['placeholder' => 'Sélectionner...']),
            BooleanField::new('isNew')->setLabel('Neuf')
                ->onlyOnForms(),
            AssociationField::new('boxCondition')
                ->setLabel('État de la boite')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->onlyOnForms(),
            AssociationField::new('equipmentCondition')
                ->setLabel('État des pièces')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->onlyOnForms(),
            AssociationField::new('gameRule')
                ->setLabel('Règle du jeu')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->onlyOnForms(),
            DateTimeField::new('movementTime')
                ->setLabel('Date de mouvement')
                ->setFormat('dd-MM-yyy à HH:mm' )
                ->onlyOnForms(),
            MoneyField::new('movementPrice')
                ->setLabel('Prix du mouvement')
                ->setCurrency('EUR')->setStoredAsCents()
                ->onlyOnForms(),
            AssociationField::new('meansOfPaiement')
                ->setLabel('Moyen de paiement')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->onlyOnForms(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des occasions')
            ->setPageTitle('new', 'Nouvel occasion')
            ->setPageTitle('edit', 'Édition d\'un occasion')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ->remove(Crud::PAGE_INDEX, Action::DELETE);
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN');
        
    }
}

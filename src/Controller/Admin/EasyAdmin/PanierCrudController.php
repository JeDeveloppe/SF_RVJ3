<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Panier;
use phpDocumentor\Reflection\Types\Integer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PanierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Panier::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('createdAt','Fin de validité')->setFormat('dd.MM.yyyy à HH:mm:ss'),
            MoneyField::new('priceWithoutTax')->setCurrency('EUR')->setStoredAsCents(),
            IntegerField::new('qte'),
            MoneyField::new('unitPriceExclusingTax')->setCurrency('EUR')->setStoredAsCents()->onlyOnDetail(),
            AssociationField::new('occasion'),
            AssociationField::new('item'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des paniers en cours')
            ->setPageTitle('detail', 'Détails d\'une ligne de panier')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields([])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);        
    }
}

<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\DocumentLineTotals;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DocumentLineTotalsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DocumentLineTotals::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('itemsWeigth')->setLabel('Poid des articles'),
            IntegerField::new('itemsPriceWithoutTax')->setLabel('Prix HT articles'),
            IntegerField::new('occasionsWeigth')->setLabel('Poid des occasions'),
            IntegerField::new('occasionsPriceWithoutTax')->setLabel('Prix HT occasions'),
            IntegerField::new('boitesWeigth')->setLabel('Boites des pièces dét.'),
            IntegerField::new('boitesPriceWithoutTax')->setLabel('Prix HT pièces dét.'),
            IntegerField::new('discountonpurchase')->setLabel('Remise de qté'),
            IntegerField::new('discountonpurchaseinpurcentage')->setLabel('% de remise:'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des totaux des documents')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields([])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);        
    }
}

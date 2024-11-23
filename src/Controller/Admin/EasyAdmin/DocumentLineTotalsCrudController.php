<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\DocumentLineTotals;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class DocumentLineTotalsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DocumentLineTotals::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('document')->setLabel('Document')->setTextAlign('center'),
            IntegerField::new('itemsWeigth')->setLabel('Poid articles (en g)')->setTextAlign('center'),
            MoneyField::new('itemsPriceWithoutTax')
                ->setLabel('Prix HT articles')
                ->setCurrency('EUR')
                ->setStoredAsCents()->setTextAlign('center'),
            IntegerField::new('occasionsWeigth')->setLabel('Poid occasions (en g)')->setTextAlign('center'),
            MoneyField::new('occasionsPriceWithoutTax')
                ->setCurrency('EUR')
                ->setStoredAsCents()
                ->setLabel('Prix HT occasions')->setTextAlign('center'),
            IntegerField::new('boitesWeigth')->setLabel('Poid boite (en g)')->setTextAlign('center'),
            MoneyField::new('boitesPriceWithoutTax')
                ->setCurrency('EUR')
                ->setStoredAsCents()
                ->setLabel('Prix HT pièces dét.')->setTextAlign('center'),
            MoneyField::new('discountonpurchase')
                ->setLabel('Remise de qté:')
                ->setCurrency('EUR')
                ->setStoredAsCents()->setTextAlign('center'),
            IntegerField::new('discountonpurchaseinpurcentage')->setLabel('% de remise (qté):')->setTextAlign('center'),
            MoneyField::new('voucherDiscountValueUsed')->setLabel('Bon d\'achat')
            ->setDisabled(true)
            ->setCurrency('EUR')
            ->setStoredAsCents()->setTextAlign('center'),
            AssociationField::new('voucherDiscounts')->setLabel('Bon d\'achat en détail')->setTextAlign('center')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des détails des documents')
            ->setPageTitle('detail', 'Détails d\'une vente')
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

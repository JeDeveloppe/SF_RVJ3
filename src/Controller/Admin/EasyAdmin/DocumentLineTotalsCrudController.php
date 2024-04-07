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
            IntegerField::new('itemsWeigth')->setLabel('Poid des articles (en g)'),
            MoneyField::new('itemsPriceWithoutTax')
                ->setLabel('Prix HT articles')
                ->setCurrency('EUR')
                ->setStoredAsCents(),
            IntegerField::new('occasionsWeigth')->setLabel('Poid des occasions (en g)'),
            MoneyField::new('occasionsPriceWithoutTax')
                ->setCurrency('EUR')
                ->setStoredAsCents()
                ->setLabel('Prix HT occasions'),
            IntegerField::new('boitesWeigth')->setLabel('Boites des articles (en g)'),
            MoneyField::new('boitesPriceWithoutTax')
                ->setCurrency('EUR')
                ->setStoredAsCents()
                ->setLabel('Prix HT pièces dét.'),
            MoneyField::new('discountonpurchase')
                ->setLabel('Remise de qté:')
                ->setCurrency('EUR')
                ->setStoredAsCents(),
            IntegerField::new('discountonpurchaseinpurcentage')->setLabel('% de remise (qté):'),
            MoneyField::new('voucherDiscountValueUsed')->setLabel('Bon d\'achat')
            ->setDisabled(true)
            ->setCurrency('EUR')
            ->setStoredAsCents(),
            AssociationField::new('voucherDiscounts')->setLabel('Bon d\'achat en détail')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des totaux des documents')
            ->setPageTitle('detail', 'Totaux d\'une vente')
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

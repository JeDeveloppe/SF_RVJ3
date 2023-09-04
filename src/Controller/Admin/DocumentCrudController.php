<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use phpDocumentor\Reflection\Types\Integer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('token')->setLabel('Token')->setDisabled(true)->onlyOnDetail(),
            IntegerField::new('quoteNumber')->setLabel('Num. devis')->setDisabled(true),
            IntegerField::new('BillNumber')->setLabel('Num. facture')->setDisabled(true),
            MoneyField::new('totalExcludingTax')->setLabel('Total HT')->setDisabled(true)->setCurrency('EUR')->setStoredAsCents()->onlyOnDetail(),
            MoneyField::new('totalWithTax')->setLabel('Total TTC')->setDisabled(true)->setCurrency('EUR')->setStoredAsCents(),
            MoneyField::new('deliveryPriceExcludingTax')->setLabel('Frais de port')->setCurrency('EUR')->setStoredAsCents()->setDisabled(true),
            BooleanField::new('isQuoteReminder')->setLabel('Devis relancer')->setDisabled(true)->onlyOnDetail(),
            DateTimeField::new('endOfQuoteValidation')->setLabel('Fin de validation du devis')->setDisabled(true)->onlyOnDetail(),
            DateTimeField::new('createdAt')->setLabel('Devis créé le')->setDisabled(true)->onlyOnDetail(),
            DateTimeField::new('timeOfSendingQuote')->setLabel('Devis relancé le')->setDisabled(true)->onlyOnDetail(),
            BooleanField::new('isDeleteByUser')->setLabel('Supprimer par l\'utilisateur')->setDisabled(true)->onlyOnDetail(),
            TextField::new('message')->setLabel('Message')->setDisabled(true)->onlyOnDetail(),
            AssociationField::new('taxRate')->setLabel('Taux de tva')->setDisabled(true)->onlyOnDetail(),
            MoneyField::new('cost')->setLabel('Coût')->setDisabled(true)->setCurrency('EUR')->setStoredAsCents()->onlyOnDetail(),
            TextField::new('deliveryAddress')->setLabel('Adresse de livraison')->setDisabled(true)->onlyOnDetail(),
            TextField::new('billingAddress')->setLabel('Adresse de facturation')->setDisabled(true)->onlyOnDetail(),
            AssociationField::new('sendingMethod')->setLabel('Méthode d\'envoi')->setDisabled(true)->onlyOnDetail(),
            AssociationField::new('documentStatus')->setLabel('Status du document')->setDisabled(true),
            AssociationField::new('payment')->setLabel('Paiement')->setDisabled(true),
            TextField::new('tokenPayment')->setLabel('Token du paiement')->setDisabled(true)->onlyOnForms(),
            AssociationField::new('user')->setLabel('Client')->setDisabled(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des documents')
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

<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Payment;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PaymentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Payment::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('timeOfTransaction')->setLabel('Date de paiement')->setFormat('dd.MM.yyyy à HH:mm:ss'),
            AssociationField::new('document')->setLabel('Document (n° devis)')
                ->onlyOnForms()
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->setQueryBuilder(
                        fn(QueryBuilder $queryBuilder) => 
                        $queryBuilder
                        ->where('entity.payment IS NULL')
                        ->orderBy('entity.quoteNumber', 'ASC')
                    ),
            AssociationField::new('document')->setLabel('Document (n° facture)')->hideOnForm(),
            AssociationField::new('meansOfPayment')
                ->setLabel('Par')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->renderAsEmbeddedForm(),
            TextField::new('tokenPayment')
                ->setLabel('Token de paiement')
                ->setFormTypeOptions(['attr' => ['value' => 'RefaitesVosJeuxManuel']])
                ->setDisabled(true),
            DateTimeField::new('createdAt')->setLabel('Enregistré le')->setFormat('dd.MM.yyyy à HH:mm:ss')->onlyOnDetail(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des paiements')
            ->setPageTitle('new', 'Nouveau paiement')
            ->setPageTitle('edit', 'Édition d\'un paiement')
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewInvoice = Action::new('viewInvoice', 'Détails', 'fa fa-file-invoice')
            ->linkToRoute('admin_invoice_details', function (Payment $payment): array {
                return [
                    'token' => $payment->getTokenPayment(),
                ];
            });

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $viewInvoice)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN');
        
    }
}

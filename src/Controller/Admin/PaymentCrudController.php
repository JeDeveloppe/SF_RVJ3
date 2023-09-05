<?php

namespace App\Controller\Admin;

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
            DateTimeField::new('createdAt')->setLabel('Enregistré le')->setFormat('dd.MM.yyyy à HH:mm:ss'),
            AssociationField::new('document')->setLabel('Document<br/>(n° facture)')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->setQueryBuilder(
                        fn(QueryBuilder $queryBuilder) => 
                        $queryBuilder
                        ->where('entity.payment IS NULL')
                            ->orderBy('entity.quoteNumber', 'ASC')
                    ),
            TextField::new('tokenPayment')->setLabel('Token de paiement'),
            DateTimeField::new('timeOfTransaction')->setLabel('Date de paiement')->setFormat('dd.MM.yyyy à HH:mm:ss'),
            AssociationField::new('meansOfPayment')->setLabel('Par')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des paiements')
            ->setPageTitle('new', 'Nouveau paiement')
            ->setPageTitle('edit', 'Édition d\'un paiement')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN');
        
    }
}

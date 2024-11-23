<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\DocumentLine;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class DocumentLineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DocumentLine::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('boite'),
            AssociationField::new('occasion'),
            TextEditorField::new('question')->setLabel('Question'),
            TextEditorField::new('answer')->setLabel('Réponse'),
            MoneyField::new('priceExcludingTax')->setLabel('Prix HT')->setDisabled(true)->setCurrency('EUR'),
            IntegerField::new('quantity')->setDisabled(true)->onlyOnDetail(),
            AssociationField::new('document'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setDefaultSort(['document' => 'DESC'])
            ->setPageTitle('index', 'Lignes des documents')
            ->setPageTitle('detail', 'Détail d\'une ligne de document')
            ->setSearchFields(['id','occasion.reference', 'document.billNumber'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN');
        
    }
}

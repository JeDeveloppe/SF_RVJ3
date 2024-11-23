<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Stock;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\RequestStack;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class StockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
        
    }

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack
    )
    {
        
    }
    public function configureFields(string $pageName): iterable
    {
        $fields = [];

            $fields[] = FormField::addTab('Général');
            $fields[] = TextField::new('name', 'Nom:');
            $fields[] = FormField::addTab('Liste des occasions');
            $fields[] = CollectionField::new('occasions','Total des occasions')->setTemplatePath('admin/fields/occasions_in_stock.html.twig')->onlyOnDetail();
            $fields[] = AssociationField::new('occasions', '...en ligne')
                        ->onlyOnIndex()
                        ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                        ->autocomplete()
                        ->setQueryBuilder(
                            fn(QueryBuilder $queryBuilder) => 
                            $queryBuilder
                            ->where('entity.isOnline = :true')
                            ->setParameter('true', true)
                            ->orderBy('entity.reference', 'ASC'));

        return $fields;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des stocks du site')
            ->setPageTitle('new', 'Nouveau stock')
            ->setPageTitle('edit', 'Édition d\'un stock')
            ->setSearchFields(['occasions.reference'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN');
        
    }
    
}

<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Department;
use App\Repository\CountryRepository;
use App\Repository\GranderegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;

class DepartmentCrudController extends AbstractCrudController
{
    public function __construct(
        private GranderegionRepository $granderegionRepository,
    )
    { 
    }

    public static function getEntityFqcn(): string
    {
        return Department::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom')->setTextAlign('center'),
            SlugField::new('slug')->setTargetFieldName('name')->setTextAlign('center'),
            TextField::new('code')->setLabel('Numéro / nom')->setTextAlign('center'),
            AssociationField::new('cities')->setLabel('Nombre de villes')->onlyOnIndex()->setTextAlign('center'),
            AssociationField::new('grandeRegion')->setLabel('Grande Région')->setFormTypeOptions(['placeholder' => 'Sélectionner une Grande Région...'])->setTextAlign('center'),
            AssociationField::new('country')->setLabel('Pays')->setTextAlign('center')->onlyOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des départements')
            ->setPageTitle('new', 'Nouveau département')
            ->setPageTitle('edit', 'Édition du département')
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Department) {
            $entityInstance->setCountry($this->granderegionRepository->find($entityInstance->getGrandeRegion())->getCountry());
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}

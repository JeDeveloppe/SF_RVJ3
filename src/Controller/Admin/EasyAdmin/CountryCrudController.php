<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CountryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Country::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom')->setTextAlign('center'),
            BooleanField::new('isActifInInscriptionForm')->setLabel('Actif à \'inscription:')->setDisabled(true)->onlyOnIndex()->setTextAlign('center'),
            BooleanField::new('isActifInInscriptionForm')->setLabel('Actif à \'inscription:')->onlyOnForms(),
            TextField::new('isocode')->setLabel('Code ISO')->setTextAlign('center'),
            AssociationField::new('granderegions')->setLabel('Nbre de Grande Région')->setTextAlign('center')->onlyOnIndex(),
            AssociationField::new('departments')->setLabel('Nbre de départements')->onlyOnIndex()->setTextAlign('center'),
            //AssociationField::new('cities')->setLabel('Nombre de villes')->onlyOnIndex(),
            //AssociationField::new('users')->setLabel('Nombre de clients')->onlyOnIndex()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des pays')
            ->setPageTitle('new', 'Nouveau pays')
            ->setPageTitle('edit', 'Édition du pays')
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
        if ($entityInstance instanceof Country) {
            $entityInstance->setName(strtoupper($entityInstance->getName()));
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Country) {
            $entityInstance->setName(strtoupper($entityInstance->getName()));
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}

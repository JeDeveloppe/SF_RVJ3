<?php

namespace App\Controller\Admin;

use App\Entity\Boite;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;

class BoiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Boite::class;
    }

    public function __construct(
        private Security $security
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom'),
            SlugField::new('slug')->setTargetFieldName('name')->setLabel('Slug')->onlyOnForms(),
            IntegerField::new('year')->setLabel('Année'),
            AssociationField::new('editor')->setLabel('Éditeur')->setFormTypeOptions(['placeholder' => 'Sélectionner un éditeur...']),
            IntegerField::new('weigth')->setLabel('Poid (en g)')->onlyOnForms(),
            IntegerField::new('age')->setLabel('A partir de (âge)')->onlyOnForms(),
            IntegerField::new('players')->setLabel('Nombre de joueurs')->onlyOnForms(),
            BooleanField::new('isOccasion')->setLabel('Occasion'),
            IntegerField::new('htPrice')->setLabel('Prix HT (en cents)'),
            DateTimeField::new('createdAt')->setLabel('Créé le')->setFormat('dd-MM-yyyy')->setDisabled()->onlyOnForms(),
            AssociationField::new('createdBy')->setLabel('Créé par')->setFormTypeOption('choice_label', 'nickname')->setDisabled(true)->setFormTypeOptions(['placeholder' => 'Créateur de la boite...']),
            BooleanField::new('isDeliverable')->setLabel('Livrable')->onlyOnForms(),
            BooleanField::new('isDeee')->setLabel('Deee'),
            BooleanField::new('isDirectSale')->setLabel('Vente directe'),
            BooleanField::new('isOnline')->setLabel('En ligne')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des boites')
            ->setPageTitle('new', 'Nouvelle boite')
            ->setPageTitle('edit', 'Édition d\'une boite')

        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ->remove(Crud::PAGE_INDEX, Action::DELETE);
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Boite) {
            $user = $this->security->getUser();
            $entityInstance->setCreatedAt(new DateTimeImmutable ('now'))->setCreatedBy($user);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}

<?php

namespace App\Controller\Admin\EasyAdmin;

use DateTimeImmutable;
use App\Entity\DocumentParametre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class DocumentParametreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DocumentParametre::class;
    }

    public function __construct(
        private Security $security
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('billingTag')->setLabel('TAG pour les factures:')->setFormTypeOptions(['attr' => ['placeholder' => 'exemple: FAC']])->setTextAlign('center'),
            TextField::new('quoteTag')->setLabel('TAG pour les devis:')->setFormTypeOptions(['attr' => ['placeholder' => 'exemple: DEV']])->setTextAlign('center'),
            IntegerField::new('delayBeforeDeleteDevis')->setLabel('Délai avant relance devis (en jours)')->setTextAlign('center'),
            MoneyField::new('preparation')->setLabel('Coût de préparation:')->setTextAlign('center')->setCurrency('EUR')->setStoredAsCents(),
            AssociationField::new('updatedBy')->setLabel('Dernière modification par')->onlyOnForms()->setDisabled(true)->setTextAlign('center'),
            DateTimeField::new('updatedAt')->setLabel('Dernière modification enregistrée le')->onlyOnForms()->setDisabled(true)->setTextAlign('center'),
            BooleanField::new('isOnline')->setLabel('En utilisation')->setTextAlign('center')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des paramètres')
            ->setPageTitle('new', 'Nouveaux paramètres')
            ->setPageTitle('edit', 'Édition de paramètres')
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
        if($entityInstance instanceof DocumentParametre) {
            $user = $this->security->getUser();
            $entityInstance->setUpdatedBy($user)->setUpdatedAt(new DateTimeImmutable('now'));

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}

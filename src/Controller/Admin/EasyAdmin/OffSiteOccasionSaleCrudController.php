<?php

namespace App\Controller\Admin\EasyAdmin;

use DateTimeImmutable;
use App\Entity\Occasion;
use Doctrine\ORM\QueryBuilder;
use App\Entity\OffSiteOccasionSale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\RequestStack;

class OffSiteOccasionSaleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OffSiteOccasionSale::class;
    }

    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
    )
    { 
    }

    public function configureFields(string $pageName): iterable
    {
        if($this->requestStack->getCurrentRequest()->get('entityId')){
            $disabled = true;
            $occasionField = AssociationField::new('occasion')->setLabel('Occasion')
            ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
            ->setDisabled($disabled);
        }else{
            $disabled = false;
            $occasionField = AssociationField::new('occasion')->setLabel('Occasion')
                                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                                ->setQueryBuilder(
                                    fn(QueryBuilder $queryBuilder) => 
                                    $queryBuilder->where('entity.isOnline = :true')
                                        ->setParameter('true', true)
                                        ->orderBy('entity.reference', 'ASC')
                                )->setDisabled($disabled);
        }
       
        return [
            DateTimeField::new('movementTime')
                ->setLabel('Date de mouvement')
                ->setFormat('dd-MM-yyy à HH:mm' )->setDisabled($disabled),
            $occasionField,
            MoneyField::new('movementPrice')
                ->setLabel('Prix du mouvement (HT)')
                ->setTextAlign('center')
                ->setCurrency('EUR')->setStoredAsCents(),
            AssociationField::new('movement')
                ->setLabel('Mouvement:')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...']),
            AssociationField::new('meansOfPaiement')
                ->setLabel('Moyen de paiement')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...']),
            DateTimeField::new('createdAt')->setLabel('Saisie le')->setFormat('dd-MM-yyyy')->onlyOnForms()->setDisabled(true),
            AssociationField::new('createdBy')->setLabel('Saisie par')->onlyOnForms()->setDisabled(true),
        ];
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des mouvements d\'occasions')
            ->setPageTitle('new', 'Nouveau mouvement d\'un occasion')
            ->setPageTitle('edit', 'Édition d\'un mouvement occasion')
            ->setDefaultSort(['createdAt' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN');
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof OffSiteOccasionSale) {
            $user = $this->security->getUser();
            $entityInstance->setCreatedAt(new DateTimeImmutable ('now'))->setCreatedBy($user);
            $entityManager->persist($entityInstance);

            $occasion = $entityInstance->getOccasion();
            $occasion->setIsOnline(false)->setOffSiteOccasionSale($entityInstance);
            $entityManager->persist($occasion);

            $entityManager->flush();
        }

    }
}

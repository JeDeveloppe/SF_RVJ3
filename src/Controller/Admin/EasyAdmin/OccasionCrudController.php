<?php

namespace App\Controller\Admin\EasyAdmin;

use DateTimeImmutable;
use App\Entity\Occasion;
use Doctrine\ORM\QueryBuilder;
use App\Service\UtilitiesService;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\RequestStack;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class OccasionCrudController extends AbstractCrudController
{   
    public static function getEntityFqcn(): string
    {
        return Occasion::class;
    }

    public function __construct(
        private RequestStack $requestStack,
        private Security $security,
        private OccasionRepository $occasionRepository,
        private UtilitiesService $utilitiesService
    )
    { 
    }

    public function configureFields(string $pageName): iterable
    {
        [$disabled, $disabledAfterBilling] = $this->utilitiesService->easyAdminLogicWhenBilling($this->requestStack, $this->occasionRepository);

        return [
            ImageField::new('boite.image')
                ->setBasePath($this->getParameter('app.path.boites_images'))
                ->onlyOnIndex()
                ->setLabel('Image')
                ->setPermission('ROLE_BENEVOLE'),
            AssociationField::new('boite')
                ->setLabel('Boite (active en occasion)')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Sélectionner...']])
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => 
                    $queryBuilder
                    ->where('entity.isOccasion = :value')
                    ->setParameter('value', true)
                    ->orderBy('entity.name', 'ASC')
                )
                ->setDisabled($disabled)
                ->renderAsNativeWidget()->onlyWhenCreating(),
            TextField::new('reference')->setLabel('Référence')->setDisabled(true),
            TextField::new('information')
                ->setLabel('Information sur l\'occasion')
                ->setDisabled($disabledAfterBilling)
                ->onlyOnForms(),
            AssociationField::new('boxCondition')
                ->setLabel('État de la boite')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Sélectionner...']])
                ->renderAsEmbeddedForm()->onlyOnForms(),
            AssociationField::new('boxCondition')
                ->setLabel('État de la boite')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Sélectionner...']])
                ->onlyOnForms()->setDisabled($disabledAfterBilling),
            AssociationField::new('equipmentCondition')
                ->setLabel('État des pièces')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Sélectionner...']])
                ->renderAsEmbeddedForm()->onlyOnForms(),
            AssociationField::new('equipmentCondition')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Sélectionner...']])
                ->setLabel('État des pièces')
                ->onlyOnForms()->setDisabled($disabledAfterBilling),
            AssociationField::new('gameRule')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Sélectionner...']])
                ->setLabel('Régle du jeu')
                ->renderAsEmbeddedForm()->onlyOnForms(),
            AssociationField::new('gameRule')
                ->setLabel('Régle du jeu')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Sélectionner...']])
                ->onlyOnForms()->setDisabled($disabledAfterBilling),
            MoneyField::new('boite.htPrice')
                ->setLabel('Prix HT en cents d\'une boite comme neuve:')
                ->setDisabled(true)
                ->setStoredAsCents()
                ->setCurrency('EUR')
                ->onlyOnForms(),
            MoneyField::new('priceWithoutTax')
                ->setLabel('Prix de vente HT en cents')
                ->setStoredAsCents()
                ->setCurrency('EUR')
                ->setDisabled($disabledAfterBilling),
            MoneyField::new('discountedPriceWithoutTax')
                ->setLabel('Prix de vente HT remiser en cents')
                ->onlyOnForms()
                ->setStoredAsCents()
                ->setCurrency('EUR')
                ->setDisabled($disabledAfterBilling)
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Mettre 0 pour aucune remise...']]),
            BooleanField::new('isOnline')
                ->setLabel('En ligne')
                ->setDisabled(true)->onlyOnIndex(),
            BooleanField::new('isOnline')
                ->setLabel('En ligne')
                ->setDisabled($disabledAfterBilling)->onlyOnForms(),
            AssociationField::new('offSiteOccasionSale')
                ->setLabel('Vendu / donner')
                ->setDisabled(true)
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...']),
            BooleanField::new('isNew')
                ->setLabel('Neuf')
                ->onlyOnForms()
                ->setDisabled($disabledAfterBilling)
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des occasions')
            ->setPageTitle('new', 'Nouvel occasion')
            ->setPageTitle('edit', 'Édition d\'un occasion')
            ->setDefaultSort(['boite.name' => 'ASC'])
            ->setSearchFields(['reference', 'boite.name', 'boite.editor.name'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_EDIT, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN');
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof Occasion) {

            $user = $this->security->getUser();
            $entityInstance->setCreatedAt(new DateTimeImmutable ('now'))->setCreatedBy($user)->setReference('to_create');
            $entityManager->persist($entityInstance);
            $entityManager->flush();

            //?puis on reflush avec la bonne référence
            $entityInstance->setReference($entityInstance->getBoite()->getId().'-'.$entityInstance->getId());
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}

<?php

namespace App\Controller\Admin\EasyAdmin;

use DateTimeImmutable;
use App\Entity\Occasion;
use Doctrine\ORM\QueryBuilder;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\RequestStack;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class OccasionCrudController extends AbstractCrudController
{   
    public static function getEntityFqcn(): string
    {
        return Occasion::class;
    }

    public function __construct(
        private RequestStack $requestStack,
        private Security $security,
        private OccasionRepository $occasionRepository
    )
    { 
    }

    public function configureFields(string $pageName): iterable
    {
        //?edition logic
        $id = $this->requestStack->getCurrentRequest()->get('entityId');
        if($id){
            $occasion = $this->occasionRepository->find($id);
            if($occasion->getIsOnline() == false){
                $disabledAfterBilling = true;
            }else{
                $disabledAfterBilling = false;
            }
            $disabled = true;
        }else{
            $disabled = false;
            $disabledAfterBilling = false;
        }


        return [
            AssociationField::new('boite')
                ->setLabel('Boite (uniquement en ligne)')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => 
                    $queryBuilder
                    ->where('entity.isOccasion = :value')
                    ->setParameter('value', true)
                    ->orderBy('entity.name', 'ASC')
                )
                ->setDisabled($disabled),
            TextField::new('reference')->setLabel('Référence')->setDisabled(true),
            TextField::new('information')->setLabel('Information sur l\'occasion'),
            AssociationField::new('boxCondition')
                ->setLabel('État de la boite')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->renderAsEmbeddedForm()->onlyOnIndex(),
            AssociationField::new('boxCondition')
                ->setLabel('État de la boite')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->onlyOnForms()->setDisabled($disabledAfterBilling),
            AssociationField::new('equipmentCondition')
                ->setLabel('État des pièces')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->renderAsEmbeddedForm()->onlyOnIndex(),
            AssociationField::new('equipmentCondition')
                ->setLabel('État des pièces')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->onlyOnForms()->setDisabled($disabledAfterBilling),
            AssociationField::new('gameRule')
                ->setLabel('Régle du jeu')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->renderAsEmbeddedForm()->onlyOnIndex(),
            AssociationField::new('gameRule')
                ->setLabel('Régle du jeu')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->onlyOnForms()->setDisabled($disabledAfterBilling),
            NumberField::new('boite.htPrice')
                ->setLabel('Prix HT en cents d\'une boite comme neuve:')
                ->setDisabled(true)
                ->onlyOnForms(),
            NumberField::new('priceWithoutTax')->setLabel('Prix de vente HT en cents')->onlyOnForms(),
            NumberField::new('discountedPriceWithoutTax')
                ->setLabel('Prix de vente HT remiser en cents')
                ->onlyOnForms()
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Mettre 0 pour aucune remise...']]),
            BooleanField::new('isOnline')->setLabel('En ligne'),
            AssociationField::new('offSiteOccasionSale')
                ->setLabel('Vendu / donner')
                ->setDisabled(true)
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...']),
            BooleanField::new('isNew')->setLabel('Neuf')->onlyOnForms()->onlyOnForms()
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

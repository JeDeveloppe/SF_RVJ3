<?php

namespace App\Controller\Admin\EasyAdmin;

use DateTimeImmutable;
use App\Entity\Reserve;
use App\Entity\Occasion;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\RequestStack;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReserveCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reserve::class;
    }

    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private AdminUrlGenerator $adminUrlGenerator
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {
        $someRepository = $this->em->getRepository(Occasion::class);

        return [
            FormField::addTab('Réserve'),
            AssociationField::new('user', 'Client')
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => 
                    $queryBuilder)->setFormTypeOptions(['placeholder' => 'Client / compte...'])->setRequired(false),
            AssociationField::new('occasions')
                ->setLabel('Occasions à mettre de côter:')
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => 
                    $queryBuilder
                    ->where('entity.isOnline = :true')
                    ->orderBy('entity.isOnline', 'ASC')
                    ->orderBy('entity.reference', 'ASC')
                    ->setParameter('true', true)
                )->setFormTypeOption('by_reference', false)->onlyWhenCreating()->setRequired(true),
            AssociationField::new('occasions')
                ->setLabel('Occasions à mettre de côter:')
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => 
                    $queryBuilder
                    ->orderBy('entity.isOnline', 'ASC')
                    ->orderBy('entity.reference', 'ASC')
                )->setFormTypeOption('by_reference', false)->onlyWhenUpdating(),
            TextField::new('content')
                ->setLabel('Commentaire:')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Nom du client / référence']]),
            FormField::addTab('Visualisation')->onlyOnDetail(),
            CollectionField::new('occasions')
                ->setLabel('Détails:')->onlyOnDetail()->setTemplatePath('admin/fields/reserve_details.html.twig')->setDisabled(true),

        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des réserves')
            ->setPageTitle('new', 'Nouvelle réserve')
            ->setPageTitle('edit', 'Édition d\'une réserve')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['occasions.reference'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {       
        $approveAction = Action::new('approve')
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->linkToCrudAction('admin_manual_invoice_start', ['reserveId' => $this->requestStack->getCurrentRequest()->get('entityId')]);

            return $actions
                ->add(Crud::PAGE_INDEX, Action::DETAIL);
            // ->add(Crud::PAGE_EDIT, $approveAction);
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Reserve) {
            
            //? pour chaque occasions on met hors ligne
            $occasions = $entityInstance->getOccasions();

            // foreach($occasions as $occasion){
            //     $occasion->setIsOnline(false);
            //     $entityManager->persist($occasion);
            // }

            $user = $this->security->getUser();
            $now = new DateTimeImmutable ('now');
            $entityInstance->setCreatedBy($user)->setCreatedAt($now);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Reserve) {

            //? pour chaque occasions on met hors ligne
            $occasions = $entityInstance->getOccasions();

            foreach($occasions as $occasion){
                $entityInstance->removeOccasion($occasion);
            }

            $entityManager->remove($entityInstance);
            $entityManager->flush();
        }
    }
}

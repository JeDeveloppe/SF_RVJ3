<?php

namespace App\Controller\Admin\EasyAdmin;

use DateTimeImmutable;
use App\Entity\Reserve;
use App\Entity\Occasion;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReserveCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reserve::class;
    }

    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {
        $someRepository = $this->em->getRepository(Occasion::class);

        return [
            AssociationField::new('occasions')
            ->setLabel('Occasions à mettre de côter:')
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => 
                $queryBuilder
                ->orderBy('entity.isOnline', 'ASC')
                ->orderBy('entity.reference', 'ASC')
            )->setFormTypeOption('by_reference', false)->onlyOnForms(),
            CollectionField::new('occasions')
                ->setLabel('Occasions mis de côté:')
                ->hideOnForm(),
            TextField::new('content')
            ->setLabel('Commentaire:')
            ->setFormTypeOptions(['attr' => ['placeholder' => 'Nom du client / référence']]),
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Reserve) {
            
            //? pour chaque occasions on met hors ligne
            $occasions = $entityInstance->getOccasions();

            foreach($occasions as $occasion){
                $occasion->setIsOnline(false);
                $entityManager->persist($occasion);
            }

            $user = $this->security->getUser();
            $now = new DateTimeImmutable ('now');
            $entityInstance->setCreatedBy($user)->setCreatedAt($now);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Reserve) {
            
            //? pour chaque occasions on met hors ligne
            $occasions = $entityInstance->getOccasions();

            foreach($occasions as $occasion){
                $occasion->setIsOnline(false);
                $entityManager->persist($occasion);
            }

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    // public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    // {
    //     if ($entityInstance instanceof Reserve) {
            
    //         //? pour chaque occasions on remet en ligne
    //         $occasions = $entityInstance->getOccasions();

    //         foreach($occasions as $occasion){
    //             $occasion->setIsOnline(true);
    //             $entityManager->persist($occasion);
    //         }

    //         $entityManager->remove($entityInstance);
    //         $entityManager->flush();
    //     }
    // }
}

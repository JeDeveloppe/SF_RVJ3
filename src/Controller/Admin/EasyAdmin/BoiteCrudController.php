<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Boite;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use App\Repository\BoiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
            TextField::new('image')->onlyOnIndex(),
            ImageField::new('image')->onlyOnForms()
                ->setBasePath('./public/build/images/boites/')
                ->setUploadDir('./public/build/images/boites/'),
            TextField::new('name')->setLabel('Nom'),
            SlugField::new('slug')->setTargetFieldName('name')->setLabel('Slug')->onlyOnForms(),
            IntegerField::new('year')->setLabel('Année'),
            AssociationField::new('editor')
                ->setLabel('Éditeur')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner un éditeur...'])
                ->onlyOnForms(),
            AssociationField::new('editor')
                ->setLabel('Éditeur')
                ->renderAsEmbeddedForm()->onlyOnIndex(),
            TextareaField::new('content')->setLabel('Contenu d\'une boite entière')->onlyOnForms(),
            TextField::new('contentMessage')->setLabel('Message d\'alerte sur le contenu de la boite')->onlyOnForms(),
            IntegerField::new('weigth')->setLabel('Poid (en g)')->onlyOnForms(),
            IntegerField::new('age')->setLabel('A partir de (âge)')->onlyOnForms(),
            AssociationField::new('players')->setLabel('A partir de (joueurs)')->onlyOnForms(),
            AssociationField::new('documentLines')
            ->setLabel('Nbre de demandes')->setDisabled(true),
            BooleanField::new('isOccasion')->setLabel('En occasion'),
            IntegerField::new('htPrice')->setLabel('Prix HT (en cents) d\'une boite complête en bon état')->onlyOnForms(),
            DateTimeField::new('createdAt')->setLabel('Créé le')
                ->setFormat('dd-MM-yyyy')
                ->setDisabled()
                ->onlyOnForms(),
            AssociationField::new('createdBy')->setLabel('Créé par')
                ->setFormTypeOption('choice_label', 'nickname')
                ->setDisabled(true)
                ->setFormTypeOptions(['placeholder' => 'Créateur de la boite...'])
                ->onlyOnForms(),
            BooleanField::new('isDeliverable')->setLabel('Livrable')->onlyOnForms(),
            BooleanField::new('isDeee')->setLabel('Deee'),
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
            ->setDefaultSort(['id' => 'DESC'])
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

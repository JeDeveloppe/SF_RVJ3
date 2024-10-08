<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Boite;
use DateTimeImmutable;
use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BoiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Boite::class;
    }

    public function __construct(
        private Security $security,
        private UserService $userService
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {

        //?gestion possibilité d'afficher ou pas en function du role
        $disabledWhenBenevole = $this->userService->disabledFieldWhenBenevole();

        return [
            FormField::addTab('Général'),
            IdField::new('rvj2id')->setDisabled(true)->onlyWhenUpdating(),
            IdField::new('id')->setDisabled(true),
            ImageField::new('image')
                ->setBasePath($this->getParameter('app.path.boites_images'))
                ->onlyOnIndex()
                ->setPermission('ROLE_BENEVOLE'),
            TextField::new('imageFile')
                ->setFormType(VichImageType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'allow_delete' => false,
                    'delete_label' => 'Supprimer du serveur ?',
                    'download_label' => '...',
                    'download_uri' => true,
                    'image_uri' => true,
                    // 'imagine_pattern' => '...',
                    'asset_helper' => true,
                ])
                ->setLabel('Image')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            BooleanField::new('isOnline')
                ->renderAsSwitch(false)
                ->setLabel('En ligne (pièces détachée)')
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6)
                ->onlyOnIndex(),
            BooleanField::new('isOnline')
                ->setLabel('En ligne (pièces détachée)')
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6)
                ->onlyOnForms(),
            TextField::new('name')  
                ->setLabel('Nom')
                ->setPermission('ROLE_BENEVOLE')
                ->setDisabled($disabledWhenBenevole)
                ->setColumns(6),
            SlugField::new('slug')
                ->setTargetFieldName('name')
                ->setLabel('Slug')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            IntegerField::new('year')
                ->setLabel('Année')
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            AssociationField::new('editor')
                ->setLabel('Éditeur')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner un éditeur...'])
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            AssociationField::new('editor')
                ->setLabel('Éditeur')
                ->renderAsEmbeddedForm()
                ->setPermission('ROLE_ADMIN')
                ->onlyOnIndex(),
            TextareaField::new('content')
                ->setLabel('Contenu d\'une boite entière')
                ->onlyOnForms()
                ->setColumns(6),
            TextField::new('contentMessage')
                ->setLabel('Message d\'alerte sur le contenu de la boite')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            IntegerField::new('age')
                ->setLabel('A partir de (âge)')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            UrlField::new('linktopresentationvideo')
                ->setLabel('Lien vers la vidéo de présentation du jeu:')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            AssociationField::new('playersMin')
                ->setLabel('A partir de (joueurs)')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => 
                    $queryBuilder
                        // ->where('entity.isInOccasionFormSearch = :true')
                        // ->setParameter('true', true)
                        ->orderBy('entity.orderOfAppearance', 'ASC'))
                // ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            AssociationField::new('playersMax')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => 
                    $queryBuilder
                        // ->where('entity.isInOccasionFormSearch = :true')
                        // ->setParameter('true', true)
                        ->orderBy('entity.orderOfAppearance', 'ASC'))
                ->setLabel('Jusqu\'à (joueurs)')
                // ->onlyOnForms()
                ->setRequired(true)
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            AssociationField::new('durationGame')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                ->setLabel('Durée de la partie')
                ->setRequired(true)
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),

            FormField::addTab('Occasion / Articles')->setPermission('ROLE_BENEVOLE'),
            BooleanField::new('isOccasion')
                ->setLabel('Disponible en occasion')
                ->onlyOnIndex()
                ->renderAsSwitch(false)
                ->setPermission('ROLE_ADMIN'),
            BooleanField::new('isOccasion')
                ->setLabel('Disponible en occasion')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN'),
            IntegerField::new('weigth')->setLabel('Poid (en g)')->onlyOnForms(),
            MoneyField::new('htPrice')
                ->setLabel('Prix HT d\'une boite complête en bon état')
                ->setStoredAsCents()
                ->setCurrency('EUR')
                ->onlyOnForms(),
            AssociationField::new('itemsSecondaire')->setLabel('Articles:')->setPermission('ROLE_ADMIN')->setDisabled(true)->onlyOnForms(),

            
            FormField::addTab('Paramètres')->setPermission('ROLE_ADMIN'),
            BooleanField::new('isDeliverable')->setLabel('Livrable')->onlyOnForms()->setPermission('ROLE_ADMIN'),
            BooleanField::new('isDeee')->setLabel('Deee')->onlyOnForms()->setPermission('ROLE_ADMIN'),

            
            FormField::addTab('Ventes')->setPermission('ROLE_ADMIN'),
            AssociationField::new('documentLines', 'Nbr de ventes')->onlyOnIndex()->setPermission('ROLE_ADMIN'),
            CollectionField::new('documentLines', 'Les ventes')
                ->setTemplatePath('admin/fields/documentLines.html.twig')
                ->setDisabled(true)
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN'),

            FormField::addTab('Création / Mise à jour'),
            DateTimeField::new('createdAt')->setLabel('Créé le')
                ->setFormat('dd-MM-yyyy')
                ->setDisabled()
                ->onlyOnForms(),
            AssociationField::new('createdBy')->setLabel('Créé par')
                ->setFormTypeOption('choice_label', 'nickname')
                ->setDisabled(true)
                ->setFormTypeOptions(['placeholder' => 'Créateur de la boite...'])
                ->onlyOnForms(),
            DateTimeField::new('updatedAt')->setLabel('Mise à jour le')
                ->setFormat('dd-MM-yyyy')
                ->setDisabled()
                ->onlyOnForms(),
            AssociationField::new('updatedBy')->setLabel('Mise à jour par')
                ->setFormTypeOption('choice_label', 'nickname')
                ->setDisabled(true)
                ->setFormTypeOptions(['placeholder' => '...'])
                ->onlyOnForms(),
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
            ->setSearchFields(['name', 'editor.name','id','rvj2id']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
        
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('age')
            ->add('isOccasion')
            ->add('editor')
            ->add('durationGame')
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Boite) {

            $user = $this->security->getUser();
            $entityInstance->setCreatedAt(new DateTimeImmutable ('now'))->setCreatedBy($user)->setRvj2id('RVJ3');

            $entityManager->persist($entityInstance);
            $entityManager->flush();
            
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Boite) {
            $user = $this->security->getUser();
            $entityInstance->setUpdatedBy($user)->setUpdatedAt(new DateTimeImmutable ('now'));

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}

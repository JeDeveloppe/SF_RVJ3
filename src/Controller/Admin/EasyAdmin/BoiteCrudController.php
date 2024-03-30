<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Boite;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use App\Repository\BoiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

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
            FormField::addTab('Général'),
            ImageField::new('image')
                ->setBasePath($this->getParameter('app.path.boites_images'))
                ->onlyOnIndex()
                ->setPermission('ROLE_ADMIN'),
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
                ->setLabel('En ligne')
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            TextField::new('name')  
                ->setLabel('Nom')
                ->setPermission('ROLE_ADMIN')
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
            UrlField::new('linktopresentationvideo')
                ->setLabel('Lien vers la vidéo de présentation du jeu:')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            IntegerField::new('age')
                ->setLabel('A partir de (âge)')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),
            AssociationField::new('players')
                ->setLabel('A partir de (joueurs)')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setColumns(6),

            FormField::addTab('Occasion / Articles')->setPermission('ROLE_ADMIN'),
            BooleanField::new('isOccasion')->setLabel('Disponilbe en occasion'),
            IntegerField::new('weigth')->setLabel('Poid (en g)')->onlyOnForms(),
            IntegerField::new('htPrice')->setLabel('Prix HT (en cents) d\'une boite complête en bon état')->onlyOnForms(),
            AssociationField::new('itemsSecondaire')->setLabel('Articles:'),

            
            FormField::addTab('Paramètres')->setPermission('ROLE_ADMIN'),
            BooleanField::new('isDeliverable')->setLabel('Livrable')->onlyOnForms(),
            BooleanField::new('isDeee')->setLabel('Deee'),

            
            FormField::addTab('Ventes')->setPermission('ROLE_ADMIN'),
            AssociationField::new('documentLines')->onlyOnIndex(),
            CollectionField::new('documentLines')->setTemplatePath('admin/fields/documentLines.html.twig')->setDisabled(true)->onlyOnForms(),

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
            ->setSearchFields(['name', 'editor.name','id']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
        
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

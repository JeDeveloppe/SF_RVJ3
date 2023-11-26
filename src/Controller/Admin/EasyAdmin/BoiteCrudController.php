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
            ImageField::new('image')->setBasePath($this->getParameter('app.path.boites_images'))->onlyOnIndex(),
            TextField::new('imageFile')->setFormType(VichImageType::class)->setFormTypeOptions([
                //TODO vérifier les options
                'required' => false,
                'allow_delete' => false,
                'delete_label' => 'Supprimer du serveur ?',
                'download_label' => '...',
                'download_uri' => true,
                'image_uri' => true,
                // 'imagine_pattern' => '...',
                'asset_helper' => true,
            ])->setLabel('Image')->onlyOnForms(),
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
            IntegerField::new('age')->setLabel('A partir de (âge)')->onlyOnForms(),
            AssociationField::new('players')->setLabel('A partir de (joueurs)')->onlyOnForms(),
            BooleanField::new('isOnline')->setLabel('En ligne'),

            FormField::addTab('Occasion / Articles'),
            BooleanField::new('isOccasion')->setLabel('En occasion'),
            IntegerField::new('weigth')->setLabel('Poid (en g)')->onlyOnForms(),
            IntegerField::new('htPrice')->setLabel('Prix HT (en cents) d\'une boite complête en bon état')->onlyOnForms(),
            AssociationField::new('items')->setLabel('Articles:')->setDisabled(true),

            
            FormField::addTab('Paramètres'),
            BooleanField::new('isDeliverable')->setLabel('Livrable')->onlyOnForms(),
            BooleanField::new('isDeee')->setLabel('Deee'),

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

            FormField::addTab('Ventes'),
            AssociationField::new('documentLines')
            ->setLabel('Nbre de demandes')->setDisabled(true),
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
            ->setSearchFields(['name', 'editor.name','id'])
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

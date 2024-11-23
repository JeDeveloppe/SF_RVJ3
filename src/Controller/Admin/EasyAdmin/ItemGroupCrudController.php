<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Boite;
use DateTimeImmutable;
use App\Entity\ItemGroup;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ItemGroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ItemGroup::class;
    }

    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {

        return [  
            ImageField::new('image')->setBasePath($this->getParameter('app.path.itemGroup_images'))->onlyOnIndex(),
            TextField::new('imageFile')->setFormType(VichImageType::class)->setFormTypeOptions([
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
            DateTimeField::new('updatedAt')->setLabel('Mise à jour le')->setFormat('dd.MM.yyyy')->setTimezone('Europe/Paris')->setDisabled(true)
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des groupes d\' articles')
            ->setPageTitle('new', 'Nouveau groupe')
            ->setPageTitle('edit', 'Édition d\'un groupe')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof ItemGroup) {
            $entityInstance->setUpdatedAt(new DateTimeImmutable ('now'));
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof ItemGroup) {
            $entityInstance->setUpdatedAt(new DateTimeImmutable ('now'));
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

}

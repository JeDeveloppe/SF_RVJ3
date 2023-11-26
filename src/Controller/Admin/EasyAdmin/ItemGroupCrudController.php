<?php

namespace App\Controller\Admin\EasyAdmin;

use DateTimeImmutable;
use App\Entity\ItemGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ItemGroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ItemGroup::class;
    }

    public function __construct(
        private Security $security
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {
        return [  
            AssociationField::new('boite')
                ->setLabel('Boites principale:'),
            ImageField::new('image')->setBasePath($this->getParameter('app.path.itemGroup_images'))->onlyOnIndex(),
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof ItemGroup) {
            $user = $this->security->getUser();
            $entityInstance->setUpdatedAt(new DateTimeImmutable ('now'));

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}

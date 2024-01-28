<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Media;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function __construct(
        private Security $security,
    )
    { 
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('publishedAt')->setLabel('Date de publication:')->setRequired(true),
            AssociationField::new('badge')->setLabel('Badge:'),
            TextField::new('title')->setLabel('Titre:')->setRequired(true),
            TextareaField::new('description')->setLabel('Une petite description non obligatoire...'),
            TextField::new('link')->setLabel('Lien de la page:')->onlyOnForms(),
            BooleanField::new('isOnLine')->setLabel('En ligne:')
        ];
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des médias')
            ->setPageTitle('new', 'Nouveau média')
            ->setPageTitle('edit', 'Édition d\'un média')
            ->setDefaultSort(['publishedAt' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof Media) {

            $user = $this->security->getUser();
            $entityInstance->setCreatedAt(new DateTimeImmutable ('now'))->setCreatedBy($user);
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}

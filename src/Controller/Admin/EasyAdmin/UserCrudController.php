<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Bundle\SecurityBundle\Security;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function __construct(
        private Security $security
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {

        return [

            FormField::addTab('Infos générales'),
            IdField::new('rvj2id')->setLabel('Rvj2Id')->setDisabled(true)->onlyOnForms(),
            IdField::new('id')->setLabel('Identifiant RVJ3')->setDisabled(true)->onlyOnForms(),
            AssociationField::new('level')
                ->setLabel('Role')
                ->setPermission('ROLE_ADMIN')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Choisir un rôle']]),
            TextField::new('email')->setLabel('Adresse email')->setDisabled(true),
            TextField::new('nickname')->setLabel('Pseudo (pour les admins)')->onlyOnForms()->setFormTypeOptions(['attr' => ['placeholder' => 'Uniquement pour un admin...']]),
            TelephoneField::new('phone')->setLabel('Téléphone')->onlyOnForms(),
            DateTimeField::new('createdAt')->setLabel('Date d\'inscription')->setFormat('dd.MM.yyyy')->setDisabled(true),
            DateTimeField::new('lastvisite')->setLabel('Dernière visite')->setFormat('dd.MM.yyyy')->setDisabled(true),
            DateTimeField::new('membership')->setLabel('Abonnement jusqu\'au')->setFormat('dd.MM.yyyy')->onlyOnForms()->setDisabled(true),

            FormField::addTab('Adresses'),
            AssociationField::new('addresses')->setLabel('Adresses')->onlyOnIndex()->setColumns(12),
            CollectionField::new('addresses')->setLabel('Adresses')->onlyOnDetail()->setColumns(12),
            ArrayField::new('addresses')->setLabel('Adresses')->onlyOnForms()->setDisabled(true),
            
            FormField::addTab('Documents'),
            AssociationField::new('documents')->setLabel('Documents')->onlyOnIndex(),
            CollectionField::new('documents')->setLabel('Documents')->onlyOnForms()->setDisabled(true),
            CollectionField::new('documents')->setLabel('Documents')->onlyOnDetail(),
            // CollectionField::new('documentLines')->setTemplatePath('admin/fields/documentLines.html.twig')->setDisabled(true)->onlyOnDetail(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des clients')
            ->setPageTitle('new', 'Nouveau client')
            ->setPageTitle('edit', 'Édition du client')
            ->setSearchFields(['level.name', 'email','id','nickname']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN');
        
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            
            if(is_null($entityInstance->getLevel())){
                $role = 'ROLE_USER';
                $nickname = 'ROLE_USER #'.$entityInstance->getId();
            }else{
                $role = $entityInstance->getLevel()->getNameInDatabase();
                if(is_null($entityInstance->getLevel()->getName())){
                    $nickname = NULL;
                }else{
                    $nickname = $entityInstance->getNickname();
                }
            }

            $roleMax = [];
            $roleMax[] = $role;
            $entityInstance->setRoles($roleMax)->setNickname($nickname);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder 
    { 
        $user = $this->security->getUser();

        $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters); 
        if(in_array('ROLE_BENEVOLE',$user->getRoles())){
            $response->join('entity.level', 'l')->where("l.nameInDatabase = 'ROLE_USER'")->where("l.nameInDatabase = 'ROLE_BENEVOLE'"); 
        }
        return $response; 
    }
}

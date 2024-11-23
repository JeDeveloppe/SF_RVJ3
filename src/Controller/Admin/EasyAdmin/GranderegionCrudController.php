<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Granderegion;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;

class GranderegionCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Granderegion::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('country','Pays')->setTextAlign('center')->setFormTypeOptions(['placeholder' => 'Sélectionner un pays...'])->onlyOnForms(),
            TextField::new('name','Nom')->setTextAlign('center'),
            SlugField::new('slug')->setTargetFieldName('name')->setTextAlign('center'),
            TextField::new('codeRegion','Numéro administratif')->setTextAlign('center')->onlyOnForms(),
            AssociationField::new('departments','Nbre de départements')->setTextAlign('center')->onlyOnIndex(),
            AssociationField::new('country','Pays')->setTextAlign('center')->onlyOnIndex(),
            AssociationField::new('departments','Les départements')->setTextAlign('center')->setDisabled(true)->onlyOnForms(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des Grandes Régions')
            ->setPageTitle('new', 'Nouvelle Grande Région')
            ->setPageTitle('edit', 'Édition de la Grande Région')
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }
    
}

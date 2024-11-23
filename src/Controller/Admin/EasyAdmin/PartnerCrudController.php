<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Partner;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PartnerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Partner::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('image')->setBasePath($this->getParameter('app.path.partners_images'))->onlyOnIndex(),
            TextField::new('imageFile')->setFormType(VichImageType::class)->setLabel('Image')->onlyOnForms(),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('fullUrl')->setLabel('Adresse web')->onlyOnForms(),
            TextareaField::new('description')->setLabel('Description')->onlyOnForms(),
            TextareaField::new('collect')->setLabel('Collecte')->onlyOnForms(),
            TextareaField::new('sells')->setLabel('Vend')->onlyOnForms(),
            AssociationField::new('city')->setLabel('Ville')->setFormTypeOptions(['placeholder' => 'Sélectionner une ville...'])->onlyOnForms(),
            AssociationField::new('city')->setLabel('Ville')->renderAsEmbeddedForm()->onlyOnIndex(),
            BooleanField::new('isAcceptDonations')->setLabel('Accepte les dons')->onlyOnForms(),
            BooleanField::new('isSellsSpareParts')->setLabel('Vend des pièces détachées')->onlyOnForms(),
            BooleanField::new('isSellFullGames')->setLabel('Vend des jeux complets')->onlyOnForms(),
            BooleanField::new('isDisplayOnCatalogueWhenSearchIsNull')->setLabel('Affichage catalogue si recherche NULL'),
            BooleanField::new('isWebShop')->setLabel('Eboutique')->onlyOnForms(),
            BooleanField::new('isOnline')->setLabel('En ligne'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des partenaires')
            ->setPageTitle('new', 'Nouveau partenaire')
            ->setPageTitle('edit', 'Édition du partenaire')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }

}

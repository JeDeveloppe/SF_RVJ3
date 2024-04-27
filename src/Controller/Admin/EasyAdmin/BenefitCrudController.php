<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Benefit;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BenefitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Benefit::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextField::new('priceHt', 'Prix de la prestation'),
            TextField::new('priceInfo', 'Détail du prix'),
            TextEditorField::new('description', 'Description'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des préstations')
            ->setPageTitle('new', 'Nouvelle préstation')
            ->setPageTitle('edit', 'Édition d\'un préstation')
            ->setDefaultSort(['id' => 'ASC'])
        ;
    }
}

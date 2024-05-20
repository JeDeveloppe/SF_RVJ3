<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\BadgeForMediaTimeline;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BadgeForMediaTimelineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BadgeForMediaTimeline::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name','Nom:'),
            TextField::new('text','Code texte:')->setFormTypeOptions(['attr' => ['placeholder' => 'Voir sur https://fontawesome.com/']]),
            ColorField::new('bgcolor','Couleur du fond:'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Paramètre des badges')
            ->setPageTitle('new', 'Nouveau badge')
            ->setPageTitle('edit', 'Édition d\'un badge')
            ->setDefaultSort(['id' => 'ASC'])
        ;
    }
}

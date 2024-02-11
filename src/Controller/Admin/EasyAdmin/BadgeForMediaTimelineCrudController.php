<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\BadgeForMediaTimeline;
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
            TextField::new('name'),
            TextField::new('text')->setFormTypeOptions(['attr' => ['placeholder' => 'Voir sur https://fontawesome.com/']]),
            ColorField::new('bgcolor'),
        ];
    }

}

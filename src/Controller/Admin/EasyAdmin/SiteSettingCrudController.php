<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\SiteSetting;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SiteSettingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SiteSetting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            BooleanField::new('BlockEmailSending')->setLabel('Envoi des emails bloqué:'),
            TextareaField::new('marquee')->setLabel('Texte d\'absence: (laisser vide pour désactiver'),
        ];
    }
}

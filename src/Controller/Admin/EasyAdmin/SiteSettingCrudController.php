<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\SiteSetting;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

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
            TextareaField::new('marquee')
                ->setLabel('Texte de vacances:')
                ->setFormTypeOptions(['attr' => ['placeholder' => '(laisser vide pour désactiver)']]),
            TextareaField::new('fairday')
                ->setLabel('Texte pour les foires:')
                ->setFormTypeOptions(['attr' => ['placeholder' => '(laisser vide pour désactiver)']]),
            IntegerField::new('distanceMaxForOccasionBuy','Distance Max vente Occasion (en kms)')
        ];
    }
}

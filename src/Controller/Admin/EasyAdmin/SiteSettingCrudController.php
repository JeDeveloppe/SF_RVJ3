<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\SiteSetting;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
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
            TextareaField::new('marquee')
                ->setLabel('Texte de vacances:')
                ->setFormTypeOptions(['attr' => ['placeholder' => '(laisser vide pour désactiver)']]),
            TextareaField::new('fairday')
                ->setLabel('Texte pour les foires:')
                ->setFormTypeOptions(['attr' => ['placeholder' => '(laisser vide pour désactiver)']]),
            IntegerField::new('distanceMaxForOccasionBuy','Distance Max vente Occasion (en kms)'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des réglages du site')
            ->setPageTitle('new', 'Nouveau réglage')
            ->setPageTitle('edit', 'Édition d\'un réglage')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN');
        
    }
}

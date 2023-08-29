<?php

namespace App\Controller\Admin;

use App\Entity\Partner;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PartnerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Partner::class;
    }

    
    // public function configureFields(string $pageName): iterable
    // {
    //     return [
    //         TextField::new('imageFile')->setFormType(VichImageType::class)->onlyWhenCreating(),
    //         ImageField::new('imageBlob')->setBasePath('')
    //         VichImageField::new('imageFile')->onlyOnForms();
    //     ];
    // }

}

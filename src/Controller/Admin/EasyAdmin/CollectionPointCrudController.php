<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\CollectionPoint;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CollectionPointCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CollectionPoint::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('street'),
            AssociationField::new('city'),
            BooleanField::new('isActivedInCart')
        ];
    }

}

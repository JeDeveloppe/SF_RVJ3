<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('itemGroup'),
            AssociationField::new('boite'),
            TextField::new('name'),
            IntegerField::new('stockForSale'),
            IntegerField::new('priceExcludingTax'),
            IntegerField::new('weigth'),
        ];
    }
}

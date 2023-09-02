<?php

namespace App\Controller\Admin;

use App\Entity\MovementOccasion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MovementOccasionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MovementOccasion::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\DurationOfGame;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DurationOfGameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DurationOfGame::class;
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

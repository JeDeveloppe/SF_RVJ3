<?php

namespace App\Controller\Admin\EasyAdmin;

use Amenadiel\JpGraph\Text\Text;
use App\Entity\Level;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LevelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Level::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name','Nom'),
            TextField::new('nameInDatabase','Nom en base de données'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des niveaux d\'accès')
            ->setPageTitle('new', 'Nouveau niveau d\'accès')
            ->setPageTitle('edit', 'Édition d\'un niveau d\'accès')
            ->setDefaultSort(['name' => 'ASC'])
            ;
    }
}

<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Service\MailService;
use App\Entity\ResetPassword;
use App\Service\PasswordService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ResetPasswordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ResetPassword::class;
    }

    public function __construct(
        private UserRepository $userRepository,
        private MailService $mailService,
        private PasswordService $passwordService
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email')->setLabel('Email:'),
            TextField::new('uuid')->hideWhenCreating()->setLabel('Token:')->setDisabled(true),
            DateTimeField::new('createdAt')->setFormat('dd.MM.yyyy à HH:mm')->setLabel('Créé / envoyé:')->hideWhenCreating()->setDisabled(true),
            BooleanField::new('isUsed')->hideWhenCreating()->setLabel('Utilisé:')->setDisabled(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des demandes')
            ->setPageTitle('new', 'Nouvelle demande de mot de passe')
            ->setPageTitle('edit', 'Édition d\'une demande de mot de passe')
            ->setDefaultSort(['id' => 'DESC'])
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof ResetPassword) {

            $user = $this->userRepository->findOneBy(['email' => $entityInstance->getEmail()]);

            if(!$user){

                $this->addFlash('warning','Utilisateur inconnu pour l\'adresse email: '.$entityInstance->getEmail());

                $route = $this->container->get(AdminUrlGenerator::class)
                    ->setController(ResetPasswordCrudController::class)
                    ->setAction(Action::EDIT)
                    ->generateUrl();

                $this->redirect($route);

            }else{

                $this->passwordService->saveResetPasswordInDatabaseAndSendEmail($entityInstance);

            }
        }
    }
}

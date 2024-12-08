<?php

namespace App\Controller\Admin\EasyAdmin;

use App\Entity\Item;
use App\Repository\BoiteRepository;
use DateTimeImmutable;
use App\Service\ItemService;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\RequestStack;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function __construct(
        private Security $security,
        private ItemService $itemService,
        private RequestStack $requestStack,
        private BoiteRepository $boiteRepository
    )
    {
    }

    public function configureFields(string $pageName): iterable
    {
        $boiteShell = $this->requestStack->getCurrentRequest()->get('boiteShell');
        

        if($boiteShell && $this->requestStack->getCurrentRequest()->get('crudAction') == 'new'){

            $this->getContext()->getEntity()->getInstance()->addBoiteOrigine($this->boiteRepository->find($boiteShell));

        }

        return [
            FormField::addTab('Général'),
                FormField::addFieldset('Informations')->onlyWhenUpdating(),
                    IdField::new('id')->onlyWhenUpdating()->setLabel('Id')->setDisabled(true)->setColumns(6),
                    TextField::new('reference')->onlyWhenUpdating()->setLabel('Référence:')->setDisabled(true)->setColumns(6)->onlyOnIndex(),

                FormField::addFieldset('Catalogue'),
                    AssociationField::new('itemGroup')
                        ->setLabel('Groupe d\'articles:')
                        ->onlyOnForms()
                        ->setRequired(true)
                        ->setFormTypeOptions(['placeholder' => 'Faire un choix...'])
                        ->setTextAlign('center')
                        ->setColumns(6),
                    AssociationField::new('BoiteOrigine')
                        ->setLabel('Boites Originale: (doit être en ligne)')
                        ->onlyOnForms()
                        ->setColumns(6)
                        ->onlyOnForms()
                        ->setRequired(true)
                        ->setQueryBuilder(
                            fn(QueryBuilder $queryBuilder) => 
                            $queryBuilder
                                ->where('entity.isOnline = :true')
                                ->setParameter('true', true)
                                ->orderBy('entity.id', 'ASC')),
                    AssociationField::new('BoiteOrigine')
                        ->setLabel('Boites Originale:')
                        ->onlyOnForms()
                        ->setColumns(6)
                        ->onlyOnIndex()
                        ->setRequired(true)
                        ->setQueryBuilder(
                            fn(QueryBuilder $queryBuilder) => 
                            $queryBuilder
                                ->where('entity.isOnline = :true')
                                ->setParameter('true', true)
                                ->orderBy('entity.id', 'ASC')),
                    AssociationField::new('BoiteSecondaire')
                        ->setLabel('Boites Secondaire:')->setDisabled(true)->onlyOnForms()->setColumns(6),

                FormField::addFieldset('Détails'),
                    ImageField::new('image')->setBasePath($this->getParameter('app.path.item_images'))->onlyOnIndex(),
                    TextField::new('imageFile')->setFormType(VichImageType::class)->setFormTypeOptions([
                            'required' => false,
                            'allow_delete' => false,
                            'delete_label' => 'Supprimer du serveur ?',
                            'download_label' => '...',
                            'download_uri' => true,
                            'image_uri' => true,
                            // 'imagine_pattern' => '...',
                            'asset_helper' => true,
                        ])->setLabel('Image')->onlyWhenCreating()->setColumns(12)->setRequired(true),
                    TextField::new('imageFile')->setFormType(VichImageType::class)->setFormTypeOptions([
                            'required' => false,
                            'allow_delete' => false,
                            'delete_label' => 'Supprimer du serveur ?',
                            'download_label' => '...',
                            'download_uri' => true,
                            'image_uri' => true,
                            // 'imagine_pattern' => '...',
                            'asset_helper' => true,
                        ])
                        ->setLabel('Image')->onlyWhenUpdating()->setColumns(12)->setRequired(false),
                    TextField::new('name')
                        ->setLabel('Nom:')->setColumns(6),
                    IntegerField::new('stockForSale')
                        ->setLabel('Stock à la vente:')->setColumns(6),
                    MoneyField::new('priceExcludingTax')
                        ->setLabel('Prix unitaire HT:')
                        ->setCurrency('EUR')
                        ->setStoredAsCents()
                        ->onlyOnForms()
                        ->setColumns(6),
                    IntegerField::new('weigth')
                        ->setLabel('Poid (en gramme) => arrondir au-dessus:')->onlyOnForms()->setColumns(6),
                    AssociationField::new('Envelope')->setLabel('Enveloppe:')
                    ->setFormTypeOptions(['placeholder' => 'Sélectionner une enveloppe...'])->onlyOnForms()->setColumns(6),

            FormField::addTab('Ventes')->onlyWhenUpdating(),
                AssociationField::new('documentLines')->setLabel('Ventes')->onlyWhenUpdating()->setDisabled(true),
                // CollectionField::new('documentLines')->setTemplatePath('admin/fields/documentLines.html.twi')->setDisabled(true)->onlyOnForms(),
            
            FormField::addTab('Création / Mise à jour')->onlyWhenUpdating(),
                AssociationField::new('createdBy')->onlyWhenUpdating()->setLabel('Créé par:')->setDisabled(true),
                DateTimeField::new('createdAt')->onlyWhenUpdating()->setLabel('Créé le:')->setDisabled(true),
                AssociationField::new('updatedBy')->onlyWhenUpdating()->setLabel('Mise à jour par:')->setDisabled(true),
                DateTimeField::new('updatedAt')->onlyWhenUpdating()->setLabel('Mise à jour le:')->setDisabled(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des articles')
            ->setPageTitle('new', 'Nouvel article')
            ->setPageTitle('edit', 'Édition d\'un article')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'BoiteOrigine.name', 'BoiteSecondaire.id', 'BoiteSecondaire.name', 'reference', 'BoiteOrigine.id'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Item) {
            $user = $this->security->getUser();
            $now = new DateTimeImmutable ('now');
            $entityInstance->setCreatedAt($now)->setCreatedBy($user)->setUpdatedAt($now)->setUpdatedBy($user);

            $entityManager->persist($entityInstance);
            $entityManager->flush();

            //?on construit la référence à partir de la première boite
            $boites = $entityInstance->getBoiteOrigine();
            $reference = $this->itemService->creationReference($boites[0], $entityInstance);
            
            //?et on remet tout dans la bdd
            $entityInstance->setReference($reference);

            $entityManager->persist($entityInstance);
            $entityManager->flush();

        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Item) {
            $user = $this->security->getUser();
            $now = new DateTimeImmutable ('now');
            $entityInstance->setUpdatedAt($now)->setUpdatedBy($user);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}

<?php

namespace App\Controller\Admin\EasyAdmin;

use DateTimeImmutable;
use App\Entity\Payment;
use App\Entity\Delivery;
use App\Entity\Occasion;
use Doctrine\ORM\QueryBuilder;
use App\Service\DocumentService;
use App\Repository\TaxRepository;
use App\Entity\OffSiteOccasionSale;
use App\Repository\DocumentParametreRepository;
use App\Repository\DocumentStatusRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\ShippingMethodRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\RequestStack;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class OffSiteOccasionSaleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OffSiteOccasionSale::class;
    }

    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private TaxRepository $taxRepository,
        private ShippingMethodRepository $shippingMethodRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private DocumentParametreRepository $documentParametreRepository,
        private PaymentRepository $paymentRepository,
        private DocumentService $documentService
    )
    { 
    }

    public function configureFields(string $pageName): iterable
    {
        if($this->requestStack->getCurrentRequest()->get('entityId')){
            $disabled = true;
            $occasionField = AssociationField::new('occasion')->setLabel('Occasion')
            ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
            ->setDisabled($disabled);
            $required = false;
        }else{
            $disabled = false;
            $required = true;
            $occasionField = AssociationField::new('occasion')->setLabel('Occasion (uniquement en ligne)')
                                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])
                                ->setQueryBuilder(
                                    fn(QueryBuilder $queryBuilder) => 
                                    $queryBuilder->where('entity.isOnline = :true')
                                        ->setParameter('true', true)
                                        ->orderBy('entity.reference', 'ASC')
                                )->setDisabled($disabled);
        }
       
        return [
            DateTimeField::new('movementTime')
                ->setLabel('Date de mouvement')
                ->setFormat('dd-MM-yyy à HH:mm' )->setDisabled($disabled),
            TextField::new('placeOfTransaction')->setLabel('Lieu de vente/ don:')->setRequired($required)->setDisabled($disabled),
            $occasionField,
            IntegerField::new('movementPrice')
                ->setLabel('Prix du mouvement (HT)')
                ->setTextAlign('center')->setDisabled($disabled),
            AssociationField::new('movement')
                ->setLabel('Mouvement:')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])->renderAsEmbeddedForm(),
            AssociationField::new('meansOfPaiement')
                ->setLabel('Moyen de paiement')
                ->setFormTypeOptions(['placeholder' => 'Sélectionner...'])->renderAsEmbeddedForm(),
            DateTimeField::new('createdAt')->setLabel('Saisie le')->setFormat('dd-MM-yyyy')->onlyOnForms()->setDisabled(true),
            AssociationField::new('createdBy')->setLabel('Saisie par')->onlyOnForms()->setDisabled(true),
        ];
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des mouvements d\'occasions')
            ->setPageTitle('new', 'Nouveau mouvement d\'un occasion')
            ->setPageTitle('edit', 'Édition d\'un mouvement occasion')
            ->setDefaultSort(['createdAt' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof OffSiteOccasionSale) {

            $docParams = $this->documentParametreRepository->findOneBy([]);

            $billingAddress = 'CLIENT(E) de passage';
            $deliveryAddress = $entityInstance->getPlaceOfTransaction();
            $details = [];
            $details['panier_boites'] = ['init' => 'init']; // for action in admin
            $details['totalPanier'] = $entityInstance->getMovementPrice();
            $details['tax'] = $this->taxRepository->findOneBy([]);
            $details['deliveryCostWithoutTax'] = new Delivery();
            $details['deliveryCostWithoutTax']->setPriceExcludingTax(0);
            $details['preparationHt'] = 0;
            $details['shipping'] = $this->shippingMethodRepository->findOneBy(['name' => 'RETRAIT PENDANT UNE FOIRE']); //TODO mettre à jour comme CreationUndefined...
            $details['totauxOccasions']['weigth'] = $entityInstance->getOccasion()->getBoite()->getWeigth();
            $details['totauxOccasions']['price'] = $entityInstance->getMovementPrice();
            $details['panier_occasions'] = [];
            $details['panier_boites'] = [];
            $details['panier_items'] = [];
            $details['occasion'] = $entityInstance->getOccasion();

            $document = $this->documentService->generateDocumentInDatabaseFromSaleDuringAfair($details,$billingAddress,$deliveryAddress,$entityInstance);

            $user = $this->security->getUser();
            $entityInstance->setCreatedAt(new DateTimeImmutable ('now'))->setCreatedBy($user);
            $entityManager->persist($entityInstance);

            $occasion = $entityInstance->getOccasion();
            $occasion->setIsOnline(false)->setOffSiteOccasionSale($entityInstance);
            $entityManager->persist($occasion);

            $entityManager->flush();
        }

    }

}

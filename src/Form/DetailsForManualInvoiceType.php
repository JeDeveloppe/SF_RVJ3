<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Payment;
use App\Entity\ShippingMethod;
use App\Entity\CollectionPoint;
use App\Entity\MeansOfPayement;
use Doctrine\ORM\EntityRepository;
use DoctrineExtensions\Query\Mysql\Date;
use Symfony\Component\Form\AbstractType;
use App\Repository\ShippingMethodRepository;
use App\Repository\CollectionPointRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class DetailsForManualInvoiceType extends AbstractType
{

    public function __construct(
        private ShippingMethodRepository $shippingMethodRepository,
        private CollectionPointRepository $collectionPointRepository
        )
    {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('paiement', EntityType::class, [
                'class' => MeansOfPayement::class,
                'label' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
                'placeholder' => '-- Methode de paiement --',
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
                // 'query_builder' => function (EntityRepository $er) use ($user) {
                //     return $er->createQueryBuilder('a')
                //         ->where('a.isFacturation = :value')
                //         ->andWhere('a.user = :user')
                //         ->setParameter('user', $user)
                //         ->setParameter('value', true)
                //         ->orderBy('a.id', 'ASC');
                // },
            ])
            ->add('shippingMethod', EntityType::class, [
                'class' => ShippingMethod::class,
                'label' => false,
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
                'placeholder' => '-- Methode de livraison --',
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
                ])
            ->add('transactionDate', DateType::class, [
                'label' => 'Date de la transaction:',
                'attr' => [
                    'class' => 'col-12 form-control',
                ],
                'mapped' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
                ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider et continuer',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MeansOfPayement::class
        ]);
    }
}

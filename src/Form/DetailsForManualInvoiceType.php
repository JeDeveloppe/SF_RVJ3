<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Payment;
use App\Entity\ShippingMethod;
use App\Entity\CollectionPoint;
use App\Entity\MeansOfPayement;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\ShippingMethodRepository;
use App\Repository\CollectionPointRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
                    'class' => 'col-12',
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
                    'class' => 'col-12',
                ],
                'placeholder' => '-- Methode de livraison --',
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
                ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
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

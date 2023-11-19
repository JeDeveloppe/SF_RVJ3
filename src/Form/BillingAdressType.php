<?php

namespace App\Form;

use App\Entity\Address;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingAdressType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('addressBilling', EntityType::class, [
                'class' => Address::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'placeholder' => '-- Choisir une adresse de facturation --',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('a')
                        ->where('a.isFacturation = :value')
                        ->andWhere('a.user = :user')
                        ->setParameter('user', $user)
                        ->setParameter('value', true)
                        ->orderBy('a.id', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'user' =>  null
        ]);
    }
}

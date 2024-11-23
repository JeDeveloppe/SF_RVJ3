<?php

namespace App\Form;

use App\Entity\ShippingMethod;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class VoucherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('voucherDiscount', TextType::class, [
                'help' => 'Exemple: 0124-1234567890',
                'help_attr' => ['class' => 'small text-start'],
                'attr' => [
                    'class' => 'form-control text-center',
                    'placeholder' => 'XXXX-XXXXXXXXXX',
                ],
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Regex('/\d{4}-\d{10}/m', 'Format non correct !')
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}

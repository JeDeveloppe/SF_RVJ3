<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\Extension\Core\Type\EmailType;use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email:',
                'required' => true,
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('sujet', ChoiceType::class, [
                'label' => 'Sujet de votre demande:',
                'choices' => [
                    'PAIEMENT CHORUS' => 'PAIEMENT CHORUS',
                    'AMBASSADEUR' => ' AMBASSADEUR',
                    'DON DE JEUX' => 'DON DE JEUX',
                    'DONNÉES PERSONNELLES' => 'DONNÉES PERSONNELLES',
                    'PARTENARIAT' => 'PARTENARIAT',
                    'PRESSE'      => 'PRESSE',
                    'PRESTATIONS'      => 'PRESTATIONS',
                    'AUTRE' => 'AUTRE'
                ],
                'placeholder' => 'Sélectionner...',
                'required' => true,
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message:',
                'required' => true,
                'attr' => ['class' => 'form-control mb-5', 'rows' => 4],
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'homepage',
                // 'script_nonce_csp' => $nonceCSP,
                'locale' => 'fr',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

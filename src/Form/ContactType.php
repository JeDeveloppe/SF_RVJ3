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
                'label' => 'Votre adresse email:',
                'required' => true,
                'attr' => ['class' => 'form-control mb-3'],
            ]) //TODO sujets Antoine
            ->add('sujet', ChoiceType::class, [
                'label' => 'Sujet de votre demande:',
                'choices' => [
                    'AMBASSADEUR' => ' AMBASSADEUR',
                    'DON DE JEUX' => 'DON DE JEUX',
                    'DONNEES PERSONNELLES' => 'DONNEES PERSONNELLES',
                    'PARTENARIAT' => 'PARTENARIAT',
                    'PRESSE'      => 'PRESSE',
                    'PRESTATION'      => 'PRESTATION',
                    'AUTRE' => 'AUTRE'
                ],
                'placeholder' => 'Choisir...',
                'required' => true,
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message:',
                'required' => true,
                'attr' => ['class' => 'form-control mb-5'],
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'homepage',
                // 'script_nonce_csp' => $nonceCSP,
                'locale' => 'fr',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer votre message',
                'attr' => ['class' => 'btn btn-success mx-auto'],
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

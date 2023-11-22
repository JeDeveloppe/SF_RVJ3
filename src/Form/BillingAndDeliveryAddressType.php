<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\CollectionPoint;
use Doctrine\ORM\Mapping\Entity;
use App\Form\CollectionPointType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BillingAndDeliveryAddressType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $shipping = $options['shipping'];
        
        $builder
            ->add('addressBilling', BillingAdressType::class, ['user' => $user]);
        
        if(!is_null($shipping)){
            if($shipping->getPrice() == 'GRATUIT'){

                $builder
                    ->add('addressDelivery', CollectionPointType::class);

            }else{

                $builder
                    ->add('addressDelivery', DeliveryAdressType::class, ['user' => $user]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'user' =>  null,
            'shipping' => null
        ]);
    }
}

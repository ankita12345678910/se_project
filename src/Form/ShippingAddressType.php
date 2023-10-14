<?php

namespace App\Form;

use App\Entity\ShippingAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Name']
            ])
            ->add('addressLine1', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'House/Flat/Apt']
            ])
            ->add('addressLine2', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Locality/Area']
            ])
            ->add('landmark', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Landmark']
            ])
            ->add('cellphone', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Phone number']
            ])
            ->add('pin', NumberType::class, [
                'attr' => ['class' => 'fetch_pin_no form-control form-control-lg', 'placeholder' => 'PIN']
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'fetch_city form-control form-control-lg', 'placeholder' => 'City']
            ])
            ->add('state', TextType::class, [
                'attr' => ['class' => 'fetch_state form-control form-control-lg', 'placeholder' => 'State']
            ])
            // ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShippingAddress::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserShopkeeperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Email']
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'First name ']
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Last name ']
            ])
            ->add('cellphone', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Cellphone no']
            ])
            ->add('address', TextareaType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Address']
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Usertype: ',
                'choices' => [
                    '' => '',
                    'Shopkeeper' => 'ROLE_SHOPKEEPER',
                    'Customer' => 'ROLE_CUSTOMER',
                    'Vendor' => 'ROLE_VENDOR',
                ],
                'attr' => ['class' => 'form-control form-control-lg']

            ])
            ->add('enable', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => 'Active',
                    'Deleted' => 'Deleted',
                ],
                'attr' => ['class' => 'form-control form-control-lg']
            ]);
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
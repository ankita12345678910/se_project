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

class UserType extends AbstractType
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
            ]);
       
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

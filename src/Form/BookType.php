<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Title']
            ])
            ->add('isbn_no', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'ISBN number'],
                'label' => 'ISBN number'
            ])
            ->add('price', NumberType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Price']
            ])
            ->add('page_no', NumberType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Page number']
            ])
            ->add('edition', NumberType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Edition']
            ])
            ->add('publisher', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Publisher']
            ])
            ->add('author', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Author']
            ])
            ->add('language', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Language']
            ])
            ->add('binding', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Binding']
            ])
            // ->add('genre', TextType::class, [
            //     'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Genre']
            // ])
            ->add('available_book', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Available book']
            ])
            ->add('file', FileType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
                'label' => 'Upload File (Image file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypesMessage' => 'Please upload a valid JPJ or JPEG or PNG document',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}

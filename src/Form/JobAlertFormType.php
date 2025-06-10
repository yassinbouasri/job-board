<?php

namespace App\Form;

use App\Entity\JobAlert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobAlertFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keyword', TextType::class, [
                'label' => 'Keywords',
                'required' => false,
                'attr' => [
                    'class' => 'mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Job title, skills, or company'
                ]
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'required' => false,
                'attr' => [
                    'class' => 'mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'City, state, or remote'
                ]
            ])
            ->add('tags', CollectionType::class, [
                'label' => 'Tags',
                'required' => false,
                'entry_type' => TextType::class,
                'entry_options' => [
                    'attr' => [
                        'class' => 'mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 tag-field'
                    ]
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'attr' => [
                    'class' => 'tags-container'
                ]
            ])
            ->add('frequency', ChoiceType::class, [
                'label' => 'Notification Frequency',
                'choices' => [
                    'Daily' => 'daily',
                    'Weekly' => 'weekly'
                ],
                'attr' => [
                    'class' => 'mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobAlert::class,
        ]);
    }
}

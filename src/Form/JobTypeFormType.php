<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\User;
use App\Enums\JobTypeEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jobType', EnumType::class, [
                'class' => JobTypeEnum::class,
                'choice_label' => fn (JobTypeEnum $type) => $type->value,
                'required' => false,
                'multiple' => false,
                'placeholder' => 'Job Type',
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}

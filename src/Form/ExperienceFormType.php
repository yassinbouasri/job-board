<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\User;
use App\Enums\ExperienceEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Experience', EnumType::class, [
                'class' => ExperienceEnum::class,
                'choice_label' => fn (ExperienceEnum $experience) => $experience->value,
                'required' => false,
                'multiple' => false,
                'placeholder' => 'Experience',
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

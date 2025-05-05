<?php

namespace App\Form;

use App\Entity\Job;
use App\Enums\ExperienceEnum;
use App\Enums\JobTypeEnum;
use App\Form\DataTransformer\TagInputTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('location')
            ->add('salary')
            ->add('tags', TextType::class, [
                'required' => false,
                'attr' => [
                    'data-role' => 'tag',
                    'placeholder' => 'Tags',
                ]
            ])
            ->add('jobType', ChoiceType::class, [
                'choices' => JobTypeEnum::cases(),
                'choice_label' => fn (JobTypeEnum $type) => $type->value,
                'required' => false,
                'multiple' => true,
                'placeholder' => 'Job Type',
            ])
            ->add('experience', EnumType::class, [
                'class' => ExperienceEnum::class,
                'choice_label' => fn (ExperienceEnum $experience) => $experience->value,
                'required' => false,
                'multiple' => false,
                'placeholder' => ' ',
            ])
        ;

        $builder->get('tags')->addModelTransformer(new TagInputTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}

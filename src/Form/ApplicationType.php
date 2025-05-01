<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Job;
use App\Entity\user;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('resume')
            ->add('coverLetter')
            ->add('job', EntityType::class, [
                'class' => Job::class,
'choice_label' => 'id',
            ])
            ->add('applicant', EntityType::class, [
                'class' => user::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}

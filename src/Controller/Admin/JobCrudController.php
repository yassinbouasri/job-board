<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class JobCrudController extends AbstractCrudController
{
    #[IsGranted('ROLE_ADMIN')]
    public static function getEntityFqcn(): string
    {
        return Job::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
            Field::new('location'),
            MoneyField::new('salary')
                      ->setCurrency('EUR')
                ->setFormTypeOption('attr', [
                    'style' => 'text-align: left !important'
                ]),
            Field::new('tags'),
            DateTimeField::new('createdAt'),
            AssociationField::new('createdBy')
                        ->autocomplete()
                        ->formatValue(
                            static function ($value, ?Job $job)
                                {
                                    if (!$user = $job?->getCreatedBy()) {
                                        return null;
                                    }

                                    return $user->getEmail();
                                }
                        )
                        ->setCrudController(UserCrudController::class),
        ];
    }

}

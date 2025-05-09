<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) { }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = ['ROLE_PUBLISHER', 'ROLE_USER', 'ROLE_ADMIN'];
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('company_name')
                ->setRequired(false),
            EmailField::new('email'),
            TextField::new('password')
                ->hideOnIndex()
                ->setFormType(PasswordType::class),
            BooleanField::new('isVerified')
                ->renderAsSwitch(false),
            ChoiceField::new('roles')
                ->setChoices(array_combine($roles, $roles))
                ->allowMultipleChoices()
                ->renderExpanded()
                ->renderAsBadges()
                ->setPermission('ROLE_ADMIN'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    private function hashPassword($user): void
    {
        if ($user->getPassword()) {
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $user->getPassword())
            );
        }
    }


}

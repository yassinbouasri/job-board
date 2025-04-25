<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) { }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);
        UserFactory::createOne([
            'company_name' => 'test',
            'email' => 'example@example.com',
            'password' => $user->getPassword(),

        ]);

        UserFactory::new()->createOne([
            'company_name' => 'company',
            'email' => 'example@company.com',
            'password' => $user->getPassword(),

        ]);

        UserFactory::new()->createMany(3);
    }
}

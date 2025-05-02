<?php

namespace App\DataFixtures;

use App\Factory\ProfileFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ProfileFactory::new()->createMany(3);
    }
}

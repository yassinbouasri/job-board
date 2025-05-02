<?php

namespace App\Factory;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityRepository;
use Faker\Factory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Profile>
 */
final class ProfileFactory extends PersistentProxyObjectFactory{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Profile::class;
    }

        /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable    {
        return [
            'fullName' => self::faker()->text(20),
            'phone' => self::faker()->phoneNumber(),
            'profilePicture' => self::faker()->text(20),
            'cv' => self::faker()->text(10),
            'userProfile' => UserFactory::randomOrCreate(),
        ];
    }

        /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Profile $profile): void {})
        ;
    }
}

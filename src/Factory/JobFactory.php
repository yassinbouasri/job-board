<?php

namespace App\Factory;

use App\Entity\Job;
use App\Entity\User;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;
use function Zenstruck\Foundry\factory;

/**
 * @extends PersistentProxyObjectFactory<Job>
 */
final class JobFactory extends PersistentProxyObjectFactory{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {
        parent::__construct();
    }


    public static function class(): string
    {
        return Job::class;
    }

        /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable    {

        return [
            'description' => self::faker()->text(255),
            'location' => self::faker()->address(),
            'title' => self::faker()->text(10),
            'salary' => self::faker()->randomFloat(2, 5),
            'createdBy' => UserFactory::randomOrCreate(),
            'createdAt' => self::faker()->dateTimeBetween('-30 days', 'now')

        ];
    }

        /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Job $job): void {})
        ;
    }
}

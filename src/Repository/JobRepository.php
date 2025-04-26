<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function createPaginatedQueryBuilder(string $search = null, string $location = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('j')
            ->orderBy('j.created_at', 'DESC');

        if ($search) {
            $qb->join('j.createdBy', 'u')
               ->addSelect('u')
                ->where('u.company_name LIKE :search')
                ->orWhere('j.title LIKE :search')
                ->orWhere('j.description LIKE :search')
                ->setParameter('search', "%{$search}%");
        }


        if ($location) {
            $qb->andWhere('j.location like :location')
                ->setParameter('location', "%{$location}%");
        }

        return $qb;
    }

//    /**
//     * @return Job[] Returns an array of Job objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Job
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

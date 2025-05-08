<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
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

    public function createPaginatedQueryBuilder(
        string $sortField = null,
        string $sortDirection = null,
        ?string $search = null,
        ?string $location = null,
        ?User $createdBy = null,
        string $selectedType = null,
        string $experience = null
    ): QueryBuilder
    {
        $qb = $this->createQueryBuilder('j');

        if (!empty($sortField) || !empty($sortDirection)) {
            $qb->orderBy('j.' . $sortField, $sortDirection);
        }else{
            $qb->orderBy('j.id', 'DESC');
        }
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

        if ($createdBy) {
            $qb->andWhere('j.createdBy = :createdBy')
                ->setParameter('createdBy', $createdBy);
        }

        if ($selectedType) {
            $qb->andWhere('j.jobType LIKE :jobType')
                ->setParameter('jobType', '%' . $selectedType . '%');
        }

        if ($experience) {
            $qb->andWhere('j.experience = :experience')
                ->setParameter('experience', $experience);
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

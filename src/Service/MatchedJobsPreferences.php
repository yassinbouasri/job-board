<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\Job;
use App\Entity\JobAlert;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MatchedJobsPreferences
{
    public static function getJobs(User $user, EntityManagerInterface $em): array
    {
        $jobAlert = $em->getRepository(JobAlert::class)->findBy([
            'usr' => $user,
        ]);

        $job = [];

        foreach ($jobAlert as $alert) {
            $results = $em->getRepository(Job::class)->findByWildcard($alert)->getQuery()->getResult();
            $job = array_merge($job, $results);
        }

        return $job;

    }
}
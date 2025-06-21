<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\Job;
use App\Entity\JobAlert;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MatchedJobsPreferences
{
    public static function getJobs(User $user, EntityManagerInterface $em): array
    {
        $jobAlert = $em->getRepository(JobAlert::class)->findBy([
            'usr' => $user,
        ]);

        $jobs = [];

        foreach ($jobAlert as $alert) {
            $results = $em->getRepository(Job::class)->findByWildcard($alert)->getQuery()->getResult();
            $jobs = array_merge($jobs, $results);
        }


        return array_unique($jobs);

    }

    public static function createNotification(User $user, EntityManagerInterface $entityManager, array $jobs): array
    {
        $newNotificationsArray = array();
        foreach ($jobs as $job) {
            $newNotification = new Notification();
            $newNotification->setJob($job);

            $newNotificationsArray[] = $newNotification;
        }


        $entityManager->persist($newNotification);
        $entityManager->flush();

        $user->addNotification($newNotification);
        $entityManager->persist($user);


        return $newNotificationsArray;
    }
}
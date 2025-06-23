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

        $notificationRepo = $entityManager->getRepository(Notification::class);
        foreach ($jobs as $job) {
            if ($notificationRepo->findOneBy(['usr' => $user, 'job' => $job,])){
                continue;
            }
            $newNotification = new Notification();
            $newNotification->setJob($job);
            $newNotification->setUsr($user);

            $entityManager->persist($newNotification);

            $user->addNotification($newNotification);

            $newNotificationsArray[] = $newNotification;
        }


        $entityManager->flush();

        return $newNotificationsArray;
    }
}
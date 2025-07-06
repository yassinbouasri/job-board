<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\Job;
use App\Entity\User;

class CheckJob
{
    public static function belongToUser(Job $job, User $user): bool
    {
        $userJobs = $user->getJobs();
        foreach ($userJobs as $userJob) {
            if ($job->getId() === $userJob->getId()) {
                return true;
            }
        }

        return false;
    }
}
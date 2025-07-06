<?php

declare(strict_types=1);


namespace App\twig;

use App\Entity\Job;
use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension  extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('check_Job_Belong_To_User', [$this, 'checkJobBelongToUser']),
        ];
    }

    public function checkJobBelongToUser(Job $job, User $user): bool
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
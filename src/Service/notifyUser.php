<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class notifyUser
{
    public static function jobNotification(array $jobs, User $user, MailerInterface $mailer, EntityManagerInterface $entityManager): void
    {
        if (empty($jobs)) {
            return;
        }
        $alerts = $user->getJobAlerts();

        if (empty($alerts)) {
            return;
        }

        foreach ($alerts as $alert) {
            if ($alert->getLastNotifiedAt() === null){
                continue;
            }
            if ($alert->getLastNotifiedAt() <= new \DateTimeImmutable('now')) {
                return;
            }
        }

        $email = (new TemplatedEmail());
        $email->from('info@job-board.com');
        $email->to($user->getEmail());
        $email->subject('Job Board - Notification');
        $email->context([
            'jobs' => $jobs,
            'user' => $user,
        ]);
        $email->htmlTemplate('email/notify_user.html.twig');
        $mailer->send($email);

        foreach ($alerts as $alert) {
            $alert->setLastNotifiedAt(new \DateTimeImmutable('now'));
            $entityManager->flush($alert);

        }

    }
}
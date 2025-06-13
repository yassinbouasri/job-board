<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class notifyUser
{
    /**
     * @throws TransportExceptionInterface
     */
    public static function jobEmailNotification(array $jobs, User $user, MailerInterface $mailer, EntityManagerInterface $entityManager): void
    {
        $now = new \DateTimeImmutable();
        $cutoffs = [
           'daily' => $now->modify('-1 day'),
           'weekly' => $now->modify('-7 days'),
        ];
        $alerts = $user->getJobAlerts();

        if (empty($jobs) || empty($alerts)) {
            return;
        }

        $shouldNotify = false;

        foreach ($alerts as $alert) {
            dump($alert);
            if ($alert->getLastNotifiedAt() === null || $alert->getLastNotifiedAt() <= $cutoffs[$alert->getFrequency()]){
                $shouldNotify = true;
                break;
            }
        }
        if (!$shouldNotify) {
            return;
        }

        self::sendMailNotification($user, $jobs, $mailer);

        foreach ($alerts as $alert) {
            $alert->setLastNotifiedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($alert);

        }
        $entityManager->flush();

    }

    /**
     * @param  User  $user
     * @param  array  $jobs
     * @param  MailerInterface  $mailer
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    private static function sendMailNotification(User $user, array $jobs, MailerInterface $mailer): void
    {
        $email = (new TemplatedEmail());
        $email->from('info@job-board.com');
        $email->to($user->getEmail());
        $email->subject('Job Board - Notification');
        $email->context(
            [
                'jobs' => $jobs,
                'user' => $user,
            ]
        );
        $email->htmlTemplate('email/notify_user.html.twig');
        $mailer->send($email);
    }
}
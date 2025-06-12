<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class notifyUser
{
    public static function jobNotification(array $jobs, User $user, MailerInterface $mailer): void
    {
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

    }

}
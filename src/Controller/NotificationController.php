<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Service\MatchedJobsPreferences;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
final class NotificationController extends AbstractController{
    #[Route('/notification', name: 'app_notifications')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $jobs = MatchedJobsPreferences::getJobs($user, $entityManager);
        $job_alerts = $user->getJobAlerts() ?? array();
        return $this->render('notification/index.html.twig', [
            'job_alerts' => $job_alerts,
            'jobs' => $jobs,
        ]);
    }

    #[Route('/mark-as-read/{id}', name: 'notification_mark_read', methods: ['POST'])]
    public function markAsRead(Response $request, Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $notification->setIsRead(true);
        $entityManager->persist($notification);
        $entityManager->flush();

        return $this->redirectToRoute('app_notifications');
    }
}

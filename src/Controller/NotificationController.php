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

        MatchedJobsPreferences::createNotification($user, $entityManager, $jobs);

        $notifications = $user->getNotifications();

        foreach ($notifications as $notification) {
            dump($notification);
        }

        return $this->render('notification/index.html.twig', [
            'job_alerts' => $job_alerts,
            'jobs' => $jobs,
            'notifications' => $notifications,
        ]);
    }

    #[Route('/mark-as-read/{id}', name: 'notification_mark_as_read')]
    public function markAsRead(Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $notification->setIsRead(true);
        $entityManager->persist($notification);
        $entityManager->flush();

        return $this->redirectToRoute('app_notifications');
    }

    #[Route('/mark-all-as-read', name: 'mark_all_as_read')]
    public function markAllAsRead(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $notifications = $user->getNotifications() ?? array();

        foreach ($notifications as $notification){
            $notification->setIsRead(true);
        }

        $entityManager->flush();

        $this->addFlash('success', 'All notifications have been marked as read.');


        return $this->redirectToRoute('app_notifications');
    }

    #[Route('/unread-all', name: 'unread_all')]
    public function unreadAll(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $notifications = $user->getNotifications() ?? array();

        foreach ($notifications as $notification){
            $notification->setIsRead(false);
        }

        $entityManager->flush();

        $this->addFlash('success', 'All notifications have been marked as unread.');

        return $this->redirectToRoute('app_notifications');
    }
}

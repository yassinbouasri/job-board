<?php

namespace App\Controller;

use App\Entity\Bookmark;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController{
    #[Route('/job/{id}/bookmark', name: 'job_bookmark', methods: ['GET'])]
    public function bookmark(Job $job, EntityManagerInterface $em,): Response
    {

        $user = $this->getUser();

        if (!$user){
            $this->addFlash('warning', 'You must be logged in to bookmark jobs.');
            return $this->redirectToRoute('app_job_index');
        }

        $bookmark = new Bookmark();

        $user->addBookmarkedJob($bookmark, $job);
        $em->persist($bookmark);
        $em->flush();

        $this->addFlash('success', 'Job bookmarked!');

        return $this->redirectToRoute('app_job_index');
    }

    #[Route('/job/{id}/unbookmark', name: 'job_unbookmark', methods: ['GET'])]
    public function unbookmark(Job $job, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user){
            $this->addFlash('warning', 'You must be logged in to bookmark jobs.');
            return $this->redirectToRoute('app_job_index');
        }

        $bookmark = $em->getRepository(Bookmark::class)->findOneBy([
            'job' => $job,
            'usr' => $user
        ]);

        if (!$bookmark){
            $this->addFlash('warning', 'Job not found!');
            return $this->redirectToRoute('app_job_index');
        }

        $em->remove($bookmark);
        $em->flush();
        $this->addFlash('success', 'Job removed from bookmark!');

        return $this->redirectToRoute('app_job_index');
    }

    #[Route("/bookmarkList", name: 'app_bookmarkList', methods: ['GET'])]
    public function bookmarkList(EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
        $bookmarks = $em->getRepository(Bookmark::class)->findBy([
            'usr' => $user
        ]);

        $jobs = null;

        foreach ($bookmarks as $bookmark){
          $jobs[] = $bookmark->getJob();
        }


        return $this->render('bookmark/list.html.twig', [
            'jobs' => $jobs,
        ]);
    }
}

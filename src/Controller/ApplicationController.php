<?php

declare(strict_types=1);


namespace App\Controller;

use App\Entity\Application;
use App\Entity\Job;
use App\Form\ApplicationType;
use App\Repository\JobRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApplicationController extends AbstractController
{
    #[Route('/job/{id}/apply', name: 'app_apply', methods: ['GET', 'POST'])]
    public function apply(Request $request ,Job $job, EntityManagerInterface $entityManager): Response
    {
        $application  =  new Application();
        $user = $this->getUser();

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $application->setApplicant($user);
            $application->setJob($job);

            $entityManager->persist($application);
            $entityManager->flush();
            $this->addFlash('success', 'Application submitted.');

            return $this->redirectToRoute('app_job_show', ['id' => $job->getId()]);
        }
        return $this->render('application/apply.html.twig', [
            'job' => $job,
            'user' => $user,
        ]);
    }
}
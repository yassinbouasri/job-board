<?php

declare(strict_types=1);


namespace App\Controller;

use App\Entity\Application;
use App\Entity\Job;
use App\Entity\User;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use App\Repository\JobRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
#[IsGranted('ROLE_USER')]
class ApplicationController extends AbstractController
{
    #[Route('/job/{id}/apply', name: 'app_apply', methods: ['GET', 'POST'])]
    public function apply(Request $request ,Job $job, EntityManagerInterface $entityManager, SluggerInterface $slugger, ApplicationRepository $applicationRepository): Response
    {

        $application  =  new Application();
        $user = $this->getUser();

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);
        $applied = false;

        if ($applicationRepository->findBy(['applicant' => $user->getId(), 'job' => $job->getId()]))  {
            $applied = true;
            $this->redirectToRoute('app_job_show', ['id' => $job->getId()]);
        }

        if ($applied){
            $this->addFlash('warning', 'Already applied for this job.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $application->setJob($job);
            $application->setApplicant($user);
            $resume = $form->get('resume')->getData();
            if ($resume) {
                try {
                    $originalFilename = pathinfo($resume->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$resume->guessExtension();

                    $resume->move($this->getParameter('resume_directory'), $newFilename);

                    $application->setResume($newFilename);
                }catch (\Exception $exception){
                    $this->addFlash('error', $exception->getMessage());
                }

            }

            $application->setCoverLetter($form->get('coverLetter')->getData());



            $entityManager->persist($application);
            $entityManager->flush();
            $this->addFlash('success', 'Application submitted.');

            return $this->redirectToRoute('app_job_show', ['id' => $application->getJob()->getId()]);
        }
        return $this->render('application/apply.html.twig', [
            'job' => $job,
            'user' => $user,
            'form' => $form->createView(),
            'applied' => $applied,
        ]);
    }
}
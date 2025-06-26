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
use App\Service\FileHandler;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_USER')]
class ApplicationController extends AbstractController
{

    #[IsGranted('ROLE_PUBLISHER')]
    #[Route('/your-jobs', name: 'app_your_jobs_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface  $paginator): Response
    {
        $company = $this->getUser();

        $applications = $this->getUser()->getApplications();

        $jobPerPage = 5;

        $pagination = $paginator->paginate(
            $company->getJobs()->toArray() ?? [],
            $page = $request->query->getInt('page', 1),
            $jobPerPage
        );

        $totalPages = ceil($pagination->getTotalItemCount() / $jobPerPage);

        return $this->render('application/index.html.twig', [
            'pagination' => $pagination,
            'totalPages' => $totalPages,
            'page' => $page,
            'applications' => $applications,
        ]);
    }

    #[IsGranted('ROLE_PUBLISHER')]
    #[Route('/application/{id}', name: 'app_application_show', methods: ['GET'])]
    public function show(Request $request, Job $job, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $job->getApplications()->toArray() ?? [],
            $page = $request->query->getInt('page', 1),
            1,
        );
        return $this->render('application/show.html.twig', [
            'pagination' => $pagination,
            'job' => $job,
        ]);
    }
    #[Route('/job/{id}/apply', name: 'app_apply', methods: ['GET', 'POST'])]
    public function apply(Request $request ,Job $job, EntityManagerInterface $entityManager, ApplicationRepository $applicationRepository, FileHandler $fileHandler): Response
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
                $newResumePath = $fileHandler->handle($resume, 'resume_directory');
                $application->setResume($newResumePath);
            } else {
                $application->setResume($user->getProfile()->getCv());
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
<?php

namespace App\Controller;

use App\Entity\Job;
use App\Enums\ExperienceEnum;
use App\Enums\JobTypeEnum;
use App\Form\ExperienceFormType;
use App\Form\JobType;
use App\Form\JobTypeFormType;
use App\Repository\JobRepository;
use App\Repository\UserRepository;
use App\Service\EnumValues;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/job')]
#[IsGranted("ROLE_USER")]
final class JobController extends AbstractController{
    #[Route(name: 'app_job_index', methods: ['GET'])]
    public function index(Request $request,JobRepository $jobRepository, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search');

        $location = $request->query->get('location');

        $sort = $request->query->get('sort_by');

        dump($this->getSortValues($sort));


        $form = $this->createForm(JobTypeFormType::class);
        $form->handleRequest($request);

        $selectedType = EnumValues::getEnum(
            $request,
            JobTypeEnum::class ,
            $form->getName(),
            'jobType'
        );
        $experienceForm = $this->createForm(ExperienceFormType::class);
        $experienceForm->handleRequest($request);

        $selectedExperience = EnumValues::getEnum(
            $request,
            ExperienceEnum::class ,
            $experienceForm->getName(),
            'experience'
        );

        $query = $jobRepository->createPaginatedQueryBuilder(
            $search, $location,
            selectedType: $selectedType->value ?? null,
            experience: $selectedExperience->value ?? null,
        )->getQuery();
        $jobsPerPage = 2;

        $page = $request->query->getInt('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page,
            $jobsPerPage
        );

        return $this->render('job/index.html.twig', [
            'pagination' => $pagination,
            'page' => $page,
            'totalPages' => ceil($pagination->getTotalItemCount() / $jobsPerPage),
            'search' => $search,
            'location' => $location,
            'form' => $form->createView(),
            'experienceForm' => $experienceForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_job_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_PUBLISHER")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);



        $user = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()) {


            $job->setCreatedBy($user);

            $entityManager->persist($job);
            $entityManager->flush();
            $this->addFlash('success', 'Job created.');

            return $this->redirectToRoute('app_job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job/new.html.twig', [
            'job' => $job,
            'form' => $form,
            'title' => 'Create New Job',
        ]);
    }

    #[Route('/{id}', name: 'app_job_show', methods: ['GET'])]
    public function show(Job $job): Response
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }

    #[IsGranted("ROLE_PUBLISHER")]
    #[Route('/{id}/edit', name: 'app_job_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Job $job, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Job updated.');
            return $this->redirectToRoute('app_job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job/edit.html.twig', [
            'job' => $job,
            'form' => $form,
            'title' => 'Edit Job',
        ]);
    }

    #[Route('/{id}', name: 'app_job_delete', methods: ['POST'])]
    public function delete(Request $request, Job $job, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($job);
            $entityManager->flush();
            $this->addFlash('success', 'Job deleted.');
        }

        return $this->redirectToRoute('app_job_index', [], Response::HTTP_SEE_OTHER);
    }


    private function getSortValues(string $sort): array
    {
        [$sortBy, $direction] = explode('_', $sort);

        $allowedSorts = ['name','location','created'];

        if (!in_array($sortBy, $allowedSorts)) {
            return [];
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            return [];
        }

        return [$sortBy, $direction];
    }


}

<?php

namespace App\Controller;

use App\Entity\JobAlert;
use App\Entity\User;
use App\Form\JobAlertFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class JobAlertController extends AbstractController{
    #[Route('/job/alert/new', name: 'app_job_alert')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $job_alerts = $em->getRepository(JobAlert::class)->findBy([
            'usr' => $this->getUser()
        ]);
        $jobAlert = new JobAlert();

        /** @var User $user */
        $user = $this->getUser();

        $jobAlert->addUsr($user);
        $jobAlert->setTags([]);


        $form = $this->createForm(JobAlertFormType::class, $jobAlert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($jobAlert);
            $em->flush();
            $this->addFlash('success', 'Job Alert created.');
            return $this->redirectToRoute('app_job_alert');
        }

        return $this->render('job_alert/index.html.twig', [
            'form' => $form->createView(),
            'job_alerts' => $job_alerts,
        ]);
    }

    #[Route('/job/alert', name: 'app_job_alert_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $job_alerts = $em->getRepository(JobAlert::class)->findBy([
            'usr' => $this->getUser()
        ]);

        $form = $this->createForm(JobAlertFormType::class, $job_alerts);
        $form->handleRequest($request);
        return $this->render('job_alert/index.html.twig', [
            'form' => $form->createView(),
            'job_alerts' => $job_alerts,
        ]);
    }

    #[Route('/job/alert/{id}', name: 'app_job_alert_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, JobAlert $jobAlert): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jobAlert->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($jobAlert);
            $em->flush();
            $this->addFlash('success', 'Job Alert deleted.');
        }

        return $this->redirectToRoute('app_job_alert');
    }
}

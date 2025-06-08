<?php

namespace App\Controller;

use App\Entity\JobAlert;
use App\Form\JobAlertFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class JobAlertController extends AbstractController{
    #[Route('/job/alert', name: 'app_job_alert')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $jobAlert = new JobAlert();

        $jobAlert->addUsr($this->getUser());

        $form = $this->createForm(JobAlertFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($jobAlert);
            $em->flush();
            $this->addFlash('success', 'Job Alert created.');
            return $this->redirectToRoute('app_job_alert');
        }

        return $this->render('job_alert/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

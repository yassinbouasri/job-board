<?php

namespace App\Controller;

use App\Entity\JobAlert;
use App\Entity\User;
use App\Form\JobAlertFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class JobAlertController extends AbstractController{
    #[Route('/job/alert/{tab}', name: 'app_job_alert', defaults: ['tab' => 'alerts'])]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, string $tab): Response
    {
        if ($tab === 'create'){
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
                'tab' => $tab,
            ]);
        }
        if ($tab === 'alerts'){
            $job_alerts = $em->getRepository(JobAlert::class)->findBy([
                'usr' => $this->getUser()
            ]);

            $form = $this->createForm(JobAlertFormType::class);
            $form->handleRequest($request);

            $pagination = $paginator->paginate(
                $job_alerts,
                $request->query->getInt('page', 1),
                3
            );


            return $this->render('job_alert/index.html.twig', [
                'form' => $form->createView(),
                'job_alerts' => $job_alerts,
                'pagination' => $pagination,
                'tab' => $tab
            ]);
        }
        return $this->redirectToRoute('app_job_alert', ['tab' => 'alerts']);
    }

    #[Route('/{id}', name: 'app_job_alert_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, JobAlert $jobAlert): Response
    {

        if ($this->isCsrfTokenValid('delete'.$jobAlert->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($jobAlert);
            $em->flush();
            $this->addFlash('success', 'Job Alert deleted.');
        }

        return $this->redirectToRoute('app_job_alert', ['tab' => 'alerts']);
    }
}

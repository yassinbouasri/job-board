<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use App\Service\CvGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
final class DownloadGeneratedCvController extends AbstractController{
    #[Route('/download/generated/cv', name: 'app_download_generated_cv', methods: ['GET'])]
    public function download(CvGenerator $cvGenerator): Response
    {
        $user = $this->getUser();


        $pdfContent = $cvGenerator->generatePdf($user);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename=cv_' . $user->getProfile()?->getFullName() ?? rand(1,20). '.pdf');

//        return $response;
        return $this->render('cv/template.html.twig', [
            'profile' => $user->getProfile(),
        ]);
    }
}

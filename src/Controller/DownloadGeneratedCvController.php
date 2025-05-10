<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use App\Service\CvGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DownloadGeneratedCvController extends AbstractController{
    #[Route('/download/generated/cv', name: 'app_download_generated_cv', methods: ['GET'])]
    public function download(CvGenerator $cvGenerator): Response
    {
        $user = $this->getUser();

        $pdfContent = $cvGenerator->generatePdf($user);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename=cv_' . $user->getProfile()->getFullName() . '.pdf');
        return $response;
    }
}

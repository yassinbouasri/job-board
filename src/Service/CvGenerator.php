<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\Profile;
use App\Entity\User;
use Dompdf\Dompdf;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class CvGenerator
{
    public function __construct(private Environment $twig) { }

    public function generatePdf(UserInterface $user): string
    {
        $html = $this->twig->render('/cv/template.html.twig', [
            'user' => $user,
            'profile' => $user->getProfile(),
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
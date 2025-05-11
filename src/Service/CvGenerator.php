<?php

declare(strict_types=1);


namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class CvGenerator
{
    public function __construct(private Environment $twig) { }

    public function generatePdf($user): string
    {
        $html = $this->twig->render('/cv/template.html.twig', [
            'user' => $user,
            'profile' => $user->getProfile(),
        ]);
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $options->set('defaultPaperSize', 'A4');
        $options->set('defaultMediaType', 'print');
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4');
        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->output();
    }
}
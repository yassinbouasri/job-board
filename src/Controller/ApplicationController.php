<?php

declare(strict_types=1);


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApplicationController extends AbstractController
{
    #[Route('/apply/{id}', name: 'app_apply', methods: ['GET', 'POST'])]
    public function apply(): Response
    {
        return $this->render('job/index.html.twig');
    }
}
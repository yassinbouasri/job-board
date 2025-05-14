<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class VerifyEmailController extends AbstractController{
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEMail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        try {
            $verifyEmailHelper->validateEmailConfirmationFromRequest($request, $user->getId(),$user->getEmail());

        }catch (\Exception $e){
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('app_register_user');
        }

        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_job_index');
    }
}

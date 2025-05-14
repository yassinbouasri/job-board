<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register/user', name: 'app_register_user')]
    #[Route('/register/company', name: 'app_register_company')]
    public function registration(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        VerifyEmailHelperInterface $emailVerifier,
        MailerInterface $mailer,
        LoggerInterface $logger,
    ): Response {
        $user = new User();
        $isCompanyRegistraion = $request->attributes->get('_route') === 'app_register_company';
        $form = $this->createForm(
            RegistrationFormType::class,
            $user,
            [
                'is_company_registration' => $isCompanyRegistraion,
            ]
        );
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            if ($isCompanyRegistraion) {
                $this->register($form, $user, $userPasswordHasher, $entityManager, ['ROLE_USER', 'ROLE_PUBLISHER']);
            } else {
                $this->register($form, $user, $userPasswordHasher, $entityManager);
            }
            $signature = $emailVerifier->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
            );

            $email = (new TemplatedEmail())
                ->from('test@yourdomain.com')
                ->to($user->getEmail())
                ->subject('Please confirm your email address.')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context(
                    [
                        'user'      => $user,
                        'signedUrl' => $signature->getSignedUrl(),
                        'expiresAtMessageKey' => $signature->getExpirationMessageKey(),
                        'expiresAtMessageData' => $signature->getExpirationMessageData(),
                ]
                );

            try {

            $mailer->send($email);
                $logger->info('email sent', ['email' => $user->getEmail()]);
            } catch (TransportExceptionInterface $e) {
                $logger->error('email sanding failed'. $e->getMessage());
            }
            $this->addFlash('success', 'Registration successful. Please check your email to verify your account.');



            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'registrationForm' => $form,
            ]
        );
    }

    private function register(
        FormInterface $form,
        User $user,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        array $roles = ['ROLE_USER']
    ): void {
        /** @var string $plainPassword */
        $plainPassword = $form
            ->get('plainPassword')
            ->getData();

        $user->setRoles($roles);

        // encode the plain password
        $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

        $entityManager->persist($user);
        $entityManager->flush();
    }

//    #[Route('/verify/email', name: 'app_verify_email')]
//    public function verifyUserEmail(Request $request): Response
//    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//
//        // validate email confirmation link, sets User::isVerified=true and persists
//        try {
//            /** @var User $user */
//            $user = $this->getUser();
//            $this->emailVerifier->handleEmailConfirmation($request, $user);
//        } catch (VerifyEmailExceptionInterface $exception) {
//            $this->addFlash('verify_email_error', $exception->getReason());
//
//            return $this->redirectToRoute('app_register_company');
//        }
//
//        // @TODO Change the redirect on success and handle or remove the flash message in your templates
//        $this->addFlash('success', 'Your email address has been verified.');
//
//        return $this->redirectToRoute('app_register_company');
//    }
}

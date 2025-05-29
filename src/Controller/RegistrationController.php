<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
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
                        'user'                 => $user,
                        'signedUrl'            => $signature->getSignedUrl(),
                        'expiresAtMessageKey'  => $signature->getExpirationMessageKey(),
                        'expiresAtMessageData' => $signature->getExpirationMessageData(),
                    ]
                );

            $this->emailVerifier->sendEmailConfirmation("app_verify_email", $user, $email);

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

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        UserRepository $userRepository,
        VerifyEmailHelperInterface $verifyEmailHelper
    ): Response {
        $user = $userRepository->findOneBy(["id" => $request->get('id')]);

        try {

            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                (string)$user->getId(),
                (string)$user->getEmail()
            );

            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', $exception->getReason());

            return $this->redirectToRoute('app_register_user');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}

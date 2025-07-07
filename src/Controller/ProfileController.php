<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\ProfileFormType;
use App\Service\FileHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted("ROLE_USER")]
final class ProfileController extends AbstractController{

    #[Route('/profile/edit', name: 'app_profile_show', methods: ['GET'])]
    public function showProfile(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $profile = $entityManager->getRepository(Profile::class)->findOneBy(['userProfile' => $user]);

        if (!$profile) {
            $profile = new Profile();
            $profile->setUserProfile($user);
        }

        $form = $this->createForm(ProfileFormType::class, $profile);

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/profile/update', name: 'app_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, FileHandler $fileHandler
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $profile = $entityManager->getRepository(Profile::class)->findOneBy(['userProfile' => $user]);

        $isNew = false;
        if (!$profile) {
            $profile = new Profile();
            $profile->setUserProfile($user);
            $isNew = true;
        }

        $form = $this->createForm(ProfileFormType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('profilePicture')->getData();
            $cvFile = $form->get('cv')->getData();

            if ($imageFile) {
                $newProfileImage = $fileHandler->handle($imageFile, 'profile_images_directory');
                $profile->setProfilePicture($newProfileImage);
            }

            if ($cvFile) {
                $newCV = $fileHandler->handle($cvFile, 'resume_directory');
                $profile->setCv($newCV);
            }

            if ($isNew) {
                $entityManager->persist($profile);
            }

            $user->setProfile($profile);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('app_profile_show');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/profile/{id}', name: 'app_delete_picture', methods: ['POST'])]
    public function deleteProfilePicture(Profile $profile, EntityManagerInterface $entityManager): Response
    {
        $profile->setProfilePicture(null);
        $entityManager->persist($profile);
        $entityManager->flush();
        $this->addFlash('success', 'Profile picture deleted successfully.');
        return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/test-profile', name: 'test_profile')]
    public function testProfile(EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Create minimal profile without form
        $profile = new Profile();
        $profile->setUserProfile($user);
        $user->setProfile($profile);

        try {
            $em->persist($profile);
            $em->flush();
            return new Response('Test successful! Profile ID: '.$profile->getId());
        } catch (\Exception $e) {
            return new Response('TEST FAILED: '.$e->getMessage());
        }
    }
}

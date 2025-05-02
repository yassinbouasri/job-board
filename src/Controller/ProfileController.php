<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function updateOrCreate(Request $request,ProfileFormType $form, EntityManagerInterface $entityManager): Response
    {
        $profile = $this->getUser()->getProfile();
        if(!$profile){
            $profile = new Profile();
        }

        dd($profile);
        $form = $this->createForm(ProfileFormType::class, $profile);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $profile->setUserProfile($this->getUser());
            if (!empty($imageFile = $form->get('profilePicture')->getData() ?? '')) {
                $newFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('profile_images_directory'),
                    $newFileName
                );
                $profile->setProfilePicture($newFileName);
            }
            $entityManager->persist($profile);
//            $entityManager->flush();

            dump($profile);
            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

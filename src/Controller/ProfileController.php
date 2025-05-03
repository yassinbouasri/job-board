<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProfileController extends AbstractController{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function updateOrCreate(Request $request,ProfileFormType $form, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $profile = $this->getUser()->getProfile();
        if(!$profile){
            $profile = new Profile();
            $profile->setUserProfile($this->getUser());
            $isNew = true;
        }else{
            $isNew = false;
        }

//        dd($profile);
        $form = $this->createForm(ProfileFormType::class, $profile);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('profilePicture')->getData();
            $cvFile = $form->get('cv')->getData();

            if ($imageFile) {
                try {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFileName = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    $imageFile->move($this->getParameter('profile_images_directory'), $newFileName);
                    $profile->setProfilePicture($newFileName);
                }catch (\Exception $e){
                    $this->addFlash('error', $e->getMessage());
                }

            }

            if ($cvFile) {

                try {
                    $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFileName = $safeFilename.'-'.uniqid().'.'.$cvFile->guessExtension();

                    $cvFile->move($this->getParameter('resume_directory'), $newFileName);
                    $profile->setCv($newFileName);
                }catch (\Exception $e){
                    $this->addFlash('error', $e->getMessage());
                }

            }

            if ($isNew){
                $entityManager->persist($profile);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

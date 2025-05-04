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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
final class ProfileController extends AbstractController{

    public function __construct(private readonly SluggerInterface $slugger) { }

    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function updateOrCreate(Request $request,ProfileFormType $form, EntityManagerInterface $entityManager): Response
    {
        $profile = $this->getUser()->getProfile();
        /** @var  User $user */
        $user = $this->getUser();


        if(!$profile){
            $profile = new Profile();
            $profile->setUserProfile($user);
            $isNew = true;
        }else{
            $isNew = false;
        }

        $form = $this->createForm(ProfileFormType::class, $profile);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            dd($profile);

            $imageFile = $form->get('profilePicture')->getData();
            $cvFile = $form->get('cv')->getData();

            if($imageFile){
                $newProfileImage = $this->fileHandler($imageFile, 'profile_images_directory');
                $profile->setProfilePicture($newProfileImage);
            }

            if($cvFile){
                $newCV = $this->fileHandler($cvFile, 'resume_directory');
                $profile->setCv($newCV);
            }

            if ($isNew){
                $entityManager->persist($profile);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView()
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

    /**
     * @param  mixed  $imageFile
     * @param  Profile  $profile
     */
    private function fileHandler(mixed $imageFile, string $directory)
    {
            try {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFileName = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move($this->getParameter($directory), $newFileName);

                return $newFileName;
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
            return null;
    }


}

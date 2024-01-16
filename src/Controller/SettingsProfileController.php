<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Form\ProfileImageType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SettingsProfileController extends AbstractController
{
    #[Route('/settings/profile', name: 'app_profile_settings')]
    public function profile( Request $request,UserRepository $users,): Response 
    {
        /** @var User $user */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile(); // Check if user has a profile

        $form = $this->createForm(
            UserProfileType::class,
            $userProfile
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            $users->add($user);
            $this->addFlash(
                'success',
                'Your user profile settings were saved'
            );
            return $this->redirectToRoute('app_profile_settings');
        }

        return $this->render(
            'settings_profile/profile.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
    #[Route('/settings/profile-image', name: 'app_profile_settings_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(Request $request,SluggerInterface $slugger,UserRepository $users):Response
    {
         $form=$this->createForm(ProfileImageType::class);
         /** @var  User $user */
         $user=$this->getUser();
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
            $profileImageFile=$form->get('profileImage')->getData();
            if($profileImageFile)
            {
                $originalFileName=pathinfo(
                    $profileImageFile->getClientOriginalName(),
                    PATHINFO_FILENAME

                );
                $safeFileName=$slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $profileImageFile->guessExtension();
                // dd($originalFileName,$safeFileName,$newFileName);
                try {
                    $profileImageFile->move(
                        $this->getParameter('profiles_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                }

                $profile = $user->getUserProfile() ?? new UserProfile();
                $profile->setImage($newFileName);
                $user->setUserProfile($profile);
                $users->add($user);
                $this->addFlash('success', 'Your profile image was updated.');

                return $this->redirectToRoute('app_profile_settings_image');
            }
        }
         
         return $this->render(
             'settings_profile/profile_image.html.twig',
             [
                'form'=>$form->createView()
             ]
         );
    }

}
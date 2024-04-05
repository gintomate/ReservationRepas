<?php

namespace App\Controller;

use App\Form\UserMdpType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/user/profil', name: 'user_profil')]
    public function consultProfil(): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $delegue = false;
        foreach ($roles as  $role) {
            if ($role === "[ROLE_DELEGUE") {
                $delegue = true;
            }
        }

        return $this->render('user/profil.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'delegue' => $delegue
        ]);
    }
    #[Route('/user/changeMdp', name: 'user_change_mdp')]
    public function changeMdp(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserMdpType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Form is submitted and valid, process the data
            $newPassword = $form->get('newPassword')->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $newPassword
            );
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);

            // $entityManager->flush();

            // Redirect after successful password change
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/changeMdp.html.twig', [
            'form' => $form,
        ]);
    }
}

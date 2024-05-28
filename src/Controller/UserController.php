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
    // MENU 

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    //PROFIL

    #[Route('/user/profil', name: 'user_profil')]
    public function consultProfil(): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $delegue = in_array('ROLE_DELEGUE', $roles);

        return $this->render('user/profil.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'delegue' => $delegue
        ]);
    }

    // MDP CHANGE

    #[Route('/user/changeMdp', name: 'user_change_mdp')]
    public function changeMdp(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
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
            $em->persist($user);

            $em->flush();
            $this->addFlash(
                'success',
                'Votre Mot de passe a bien été changé.'
            );
            return $this->redirectToRoute('user_profil');
        }

        return $this->render('user/changeMdp.html.twig', [
            'form' => $form,
        ]);
    }
}

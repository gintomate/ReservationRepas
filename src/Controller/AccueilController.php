<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccueilController extends AbstractController
{
    //ACCUEIL

    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        //Check if already connected

        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('redirect');
        }
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    //REDIRECT AFTER LOGIN

    #[Route('/redirect', name: 'redirect')]
    public function redirectLogin(): Response
    {
        $user = $this->getUser();
        // Check user roles and redirect accordingly
        if ($user && $this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin');
        } elseif ($user && $this->isGranted('ROLE_DELEGUE')) {
            return $this->redirectToRoute('app_delegue');
        } elseif ($user && $this->isGranted('ROLE_STAGIAIRE')) {
            return $this->redirectToRoute('app_user');
        } elseif ($user && $this->isGranted('ROLE_PERSONNEL')) {
            return $this->redirectToRoute('app_user');
        } elseif ($user && $this->isGranted('ROLE_EXTERNE')) {
            return $this->redirectToRoute('app_user');
        } elseif ($user && $this->isGranted('ROLE_CUISINIER')) {
            return $this->redirectToRoute('app_cuisinier');
        } else {
            // Handle default redirection for other roles or no role
            return $this->redirectToRoute('app_accueil');
        }
    }
}

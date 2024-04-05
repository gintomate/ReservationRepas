<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    #[Route('/redirect', name: 'redirect_login')]
    public function redirectLogin(): Response
    {
        $user = $this->getUser();
        // Check user roles and redirect accordingly
        if ($user && $this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin');
        } elseif ($user && $this->isGranted('ROLE_DELEGUE')) {
            return $this->redirectToRoute('app_delegue');
        } elseif ($user && $this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_user');
        } else {
            // Handle default redirection for other roles or no role
            return $this->redirectToRoute('default_dashboard');
        }
    }
}

<?php

namespace App\Controller;

use App\Repository\SemaineReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MenuGestionController extends AbstractController
{
    #[Route('/menu/gestion', name: 'app_menu_gestion')]
    public function index(): Response
    {
        return $this->render('menu_gestion/index.html.twig', [
            'controller_name' => 'MenuGestionController',
        ]);
    }

    #[Route('/menu/creer', name: 'app_menu_creer')]
    public function creerMenu(): Response{
        return $this->render('menu_gestion/creer.html.twig');
    }
    #[Route('/menu/creer/get', name: 'app_menu_gestion_json')]
    public function creerJson(SemaineReservationRepository $semaineReservationRepository, SerializerInterface $serializer): Response{
        $semaine = $semaineReservationRepository->findAll();
        $jsonContent = $serializer->serialize($semaine, 'json');
        return new Response($jsonContent);
    }
}

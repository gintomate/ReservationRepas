<?php

namespace App\Controller;

use App\Repository\JourReservationRepository;
use App\Repository\SemaineReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CuisinierController extends AbstractController
{
    #[Route('/cuisine', name: 'app_cuisine')]
    public function index(): Response
    {
        return $this->render('cuisinier/index.html.twig', [
            'controller_name' => 'CuisinierController',
        ]);
    }

    //Cuisine Get

    #[Route('/cuisine/recap', name: 'app_cuisine_recap')]
    public function recap(): Response
    {
        return $this->render('cuisinier/recap.html.twig', [
            'controller_name' => 'CuisinierController',
        ]);
    }

    //Cuisine Json List

    #[Route('/cuisine/SemaineJson', name: 'app_cuisine_semaine_json')]
    public function recapSemaineJson(SerializerInterface $serializer, JourReservationRepository $jourReservationRepo): JsonResponse
    {
        $jourReservations = $jourReservationRepo->findAll();

        // Initialize an array to store semaine entities
        $semaines = [];

        // Iterate through each JourReservation entity
        foreach ($jourReservations as $jourReservation) {
            // Retrieve the Semaine associated with this JourReservation
            $semaine = $jourReservation->getSemaineReservation();
            //to change

            if (!in_array($semaine, $semaines, true)) {
                // Add this Semaine to the array
                $semaines[] = $semaine;
            }
        }
        $serializedSemaine = $serializer->serialize($semaines, 'json', ['groups' => 'semaine']);
        $jsonContent = json_decode($serializedSemaine, true);

        return new JsonResponse($jsonContent);
    }

    //Cuisine Json 

    #[Route('/cuisine/recap/Json/{id}', name: 'app_cuisine_recap_json')]
    public function recapJson(SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepo, int $id): JsonResponse
    {
        $semaine = $semaineReservationRepo->find($id);
        $serializedSemaine = $serializer->serialize($semaine, 'json', ['groups' => 'reservation']);
        $jsonContent = json_decode($serializedSemaine, true);

        return new JsonResponse($jsonContent);
    }
}

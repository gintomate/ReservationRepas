<?php

namespace App\Controller;

use App\Repository\JourReservationRepository;
use App\Repository\SemaineReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AdminConsultationController extends AbstractController
{
    #[Route('/admin/consultation', name: 'admin_consultation')]
    public function consult(): Response
    {
        return $this->render('menu_gestion/consultation.html.twig', [
            'controller_name' => 'AdminConsultationController',
        ]);
    }
    #[Route('/admin/consultationSemaineJson', name: 'admin_consultation_semaineJson')]
    public function consultSemaineJson(SerializerInterface $serializer, JourReservationRepository $jourReservationRepository): JsonResponse
    {
        $today = date('Y-m-d');
        $jourReservations = $jourReservationRepository->findAll();

        // Initialize an array to store semaine entities
        $semaines = [];

        // Iterate through each JourReservation entity
        foreach ($jourReservations as $jourReservation) {
            // Retrieve the Semaine associated with this JourReservation
            $semaine = $jourReservation->getSemaineReservation();
            //to change
            if ($semaine->getDateFin() > $today) {
                if (!in_array($semaine, $semaines, true)) {
                    // Add this Semaine to the array
                    $semaines[] = $semaine;
                }
            }
        }

        $serializeSemaine = $serializer->serialize($semaines, 'json', ['groups' => 'semaine']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }
    #[Route('/admin/consultationJson/{id}', name: 'admin_consultation_Json')]
    public function consultJson(SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepository, int $id): JsonResponse
    {
        $semaine = $semaineReservationRepository->find($id);
        $serializeSemaine = $serializer->serialize($semaine, 'json', ['groups' => 'semaineResa']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }
}

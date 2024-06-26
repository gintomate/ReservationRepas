<?php

namespace App\Controller;

use App\Entity\SemaineReservation;
use App\Repository\JourReservationRepository;
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
    public function consultSemaineJson(SerializerInterface $serializer, JourReservationRepository $jourReservationRepo): JsonResponse
    {
        $today = date('Y-m-d');
        $jourReservations = $jourReservationRepo->findAll();

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

        $serializeSemaine = $serializer->serialize($semaines, 'json', ['groups' => 'semaine', 'ASC' => "numeroSemaine" ]);
        $jsonContent = json_decode($serializeSemaine, true);
        usort($jsonContent, function($a, $b) {
            // Convert date strings to timestamps
            $timestampA = strtotime($a['dateDebut']);
            $timestampB = strtotime($b['dateDebut']);
        
            // Compare timestamps
            if ($timestampA === $timestampB) {
                return 0;
            }
            return ($timestampA < $timestampB) ? -1 : 1;
        });
        return new JsonResponse($jsonContent);
    }
    #[Route('/admin/consultationJson/{id}', name: 'admin_consultation_Json')]
    public function consultJson(SerializerInterface $serializer, SemaineReservation $semaineReservation): JsonResponse
    {

        $serializeSemaine = $serializer->serialize($semaineReservation, 'json', ['groups' => 'semaineResa']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }
}

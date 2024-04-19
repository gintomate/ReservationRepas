<?php

namespace App\Controller;

use App\Repository\SemaineReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserConsultationController extends AbstractController
{
    #[Route('/user/consultation', name: 'user_consultation')]
    public function consult(): Response
    {
        $user = $this->getUser();
        return $this->render('user_reservation/consultation.html.twig', [
            'controller_name' => 'UserConsultationController',
        ]);
    }
    #[Route('/user/consultationSemaineJson', name: 'user_consultation_semaineJson')]
    public function consultSemaineJson(SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        $reservations = $user->getReservations();
        $semaines = [];

        foreach ($reservations as $key => $reservation) {
            $semaine = $reservation->getSemaine();
            if (!in_array($semaine, $semaines, true)) {
                // Add this Semaine to the array
                $semaines[] = $semaine;
            }
        }
        $serializeSemaine = $serializer->serialize($semaines, 'json', ['groups' => 'semaine']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }
    #[Route('/user/consultationJson/{numero}', name: 'user_consultation_Json')]
    public function consultJson(SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepository, int $numero): JsonResponse
    {
        $user = $this->getUser();
        $reservations = $user->getReservations();
        $semaine = $semaineReservationRepository->find($numero);
        $resa = null;
        foreach ($reservations as $key => $reservation) {
            $semaineReser = $reservation->getSemaine();

            if ($semaineReser == $semaine) {
                $resa = $reservation;
                break;
            }
        }
        $serializeResa = $serializer->serialize($resa, 'json', ['groups' => 'consultation']);
        $jsonContent = json_decode($serializeResa, true);
        return new JsonResponse($jsonContent);
    }
}

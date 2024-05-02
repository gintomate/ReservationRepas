<?php

namespace App\Controller;

use App\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserConsultationController extends AbstractController
{

    //GET RESERVATION

    #[Route('/user/consultation', name: 'user_consultation')]
    public function consult(): Response
    {
        return $this->render('user_reservation/consultation.html.twig', [
            'controller_name' => 'UserConsultationController',
        ]);
    }
    //JSON FOR SEMAINE 

    #[Route('/user/consultationSemaineJson', name: 'user_consultation_semaineJson')]
    public function consultSemaineJson(SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        $reservations = $user->getReservations();
        $serializeSemaine = $serializer->serialize($reservations, 'json', ['groups' => 'semaine']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }

    //JSON FOR RESERVATION SOLO

    #[Route('/user/consultationJson/{id}', name: 'user_consultation_Json')]
    public function consultJson(SerializerInterface $serializer, Reservation $reservation): JsonResponse
    {
        $serializeResa = $serializer->serialize($reservation, 'json', ['groups' => 'consultation']);
        $jsonContent = json_decode($serializeResa, true);
        return new JsonResponse($jsonContent);
    }
}

<?php

namespace App\Controller;

use App\Repository\JourReservationRepository;
use App\Repository\SemaineReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Length;

class UserReservationController extends AbstractController
{
    #[Route('/user/reservation', name: 'app_user_reservation')]
    public function index(): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $tarifReduit = false;

        for ($i = 0; $i < count($roles); $i++) {

            if ($roles[$i] === 'ROLE_STAGIAIRE') {
                $tarifReduit = true;
            }
        }
        return $this->render('user_reservation/reservation.html.twig', [
            'controller_name' => 'UserReservationController',
            'tarifReduit' => $tarifReduit
        ]);
    }
    #[Route('/user/reservation/semaineJson', name: 'app_user_reservation_semaineJson')]
    public function reserverJson(SerializerInterface $serializer, JourReservationRepository $jourReservationRepository): Response
    {
        $currentDate = date('Y-m-d');

        $jourReservations = $jourReservationRepository->findAll();

        // Initialize an array to store semaine entities
        $semaines = [];

        // Iterate through each JourReservation entity
        foreach ($jourReservations as $jourReservation) {
            // Retrieve the Semaine associated with this JourReservation
            $semaine = $jourReservation->getSemaineReservation();
            //to change
            if ($semaine->getDateFin() > $currentDate) {
                if (!in_array($semaine, $semaines, true)) {
                    // Add this Semaine to the array
                    $semaines[] = $semaine;
                }
            }
        }

        // Check if this Semaine is not already in the $semaines array
        // to avoid duplicates

        $jsonContent = $serializer->serialize($semaines, 'json', ['groups' => 'semaine']);
        return new Response($jsonContent);
    }
    #[Route('/user/reservation/repasJson/{id}', name: 'app_user_reservation_repasJson')]
    public function repasJson(int $id, SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepository): Response
    {
        $semaine = $semaineReservationRepository->findOneBy(['numeroSemaine' => $id]);
        $jsonContent = $serializer->serialize($semaine, 'json', ['groups' => 'reservation']);
        return new Response($jsonContent);
    }
}

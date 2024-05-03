<?php

namespace App\Controller;

use App\Entity\SemaineReservation;
use App\Repository\JourReservationRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DelegueController extends AbstractController
{
    #[Route('/delegue', name: 'app_delegue')]
    public function index(): Response
    {
        return $this->render('delegue/index.html.twig', [
            'controller_name' => 'DelegueController',
        ]);
    }
    #[Route('/delegue/recap', name: 'delegue_recap')]
    public function recap(): Response
    {
        $user = $this->getUser();
        $section = $user->getUserInfo()->getPromo()->getSection();
        return $this->render('delegue/recap.html.twig', [
            'controller_name' => 'DelegueController',
            'section' => $section,
        ]);
    }
    #[Route('/delegue/SemaineJson', name: 'delegue_recap_semaine_json')]
    public function recapSemaineJson(SerializerInterface $serializer, JourReservationRepository $jourReservationRepo): JsonResponse
    {

        date_default_timezone_set("Indian/Reunion");

        $dateJour = new \DateTime();
        $jourReservations = $jourReservationRepo->findAll();

        // Initialize an array to store semaine entities
        $semaines = [];

        // Iterate through each JourReservation entity
        foreach ($jourReservations as $jourReservation) {
            // Retrieve the Semaine associated with this JourReservation
            $semaine = $jourReservation->getSemaineReservation();
            //to change
            if ($dateJour < $jourReservation->getDateJour()) {
                if (!in_array($semaine, $semaines, true)) {
                    // Add this Semaine to the array
                    $semaines[] = $semaine;
                }
            }
        }

        $serializedSemaine = $serializer->serialize($semaines, 'json', ['groups' => 'semaine']);
        $jsonContent =  json_decode($serializedSemaine, true);

        return new JsonResponse($jsonContent);
    }
    #[Route('/delegue/recapJson/{id}', name: 'delegue_recap_json')]
    public function recapJson(SerializerInterface $serializer, UserRepository $userRepo, ReservationRepository $reservationRepo, SemaineReservation $semaine): JsonResponse
    {
        $user = $this->getUser();
        $promo = $user->getUserInfo()->getPromo()->getId();


        $sectionChoisi = $userRepo
            ->findByPromo($promo);

        $semaineChoisi = $reservationRepo
            ->findBySemaine($semaine);

        $usersWithReservations = [];
        foreach ($sectionChoisi as $userSection) {
            $userReservations = $userSection->getReservations();

            if (count($userReservations) < 1) {
                $usersWithReservations[] = [
                    'user' => $userSection,
                    'montantTotal' => 0
                ];
            } else {
                foreach ($userReservations as $reservation) {

                    $montant = $reservation->getMontantTotal();
                    if (in_array($reservation, $semaineChoisi)) {
                        $usersWithReservations[] = [
                            'user' => $userSection,
                            'montantTotal' => $montant
                        ];
                        break; // No need to continue checking other reservations for this user
                    } else {
                        $usersWithReservations[] = [
                            'user' => $userSection,
                            'montantTotal' => 0
                        ];
                    }
                }
            }
        }

        $userSerialise = $serializer->serialize($usersWithReservations, 'json', ['groups' => ['userInfo', 'reservation']]);
        $jsonContent = json_decode($userSerialise, true);

        return new JsonResponse($jsonContent);
    }
}

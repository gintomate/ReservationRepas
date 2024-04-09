<?php

namespace App\Controller;

use App\Entity\RepasReserve;
use App\Entity\Reservation;
use App\Repository\JourReservationRepository;
use App\Repository\ReservationRepository;
use App\Repository\SemaineReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserReservationController extends AbstractController
{
    #[Route('/user/reservation', name: 'user_reservation')]
    public function reserver(Request $request, SemaineReservationRepository $semaineReservationRepository, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        //Get User Roles
        $user = $this->getUser();
        $roles = $user->getRoles();
        $tarifReduit = false;
        $date = new \DateTime();

        for ($i = 0; $i < count($roles); $i++) {
            if ($roles[$i] === 'ROLE_STAGIAIRE') {
                $tarifReduit = true;
            }
        }

        $formData = $request->request->all();
        if ($request->isMethod('POST')) {

            $semaineSelect =  $formData['semaine'];
            $semaine = $semaineReservationRepository->find($semaineSelect);
            $repasArray =  $this->fetchRepas($semaine);
            $existingReservation = $reservationRepository->findOneBy([
                'semaine' => $semaine,
                'Utilisateur' => $user
            ]);

            if ($existingReservation) {
                // If a reservation already exists, handle the situation accordingly
                // For example, you might want to return some error response
                return new Response('Reservation already exists', Response::HTTP_CONFLICT);
            }
            //new Reservation
            $Reservation = new Reservation;
            $Reservation->setSemaine($semaine);
            $Reservation->setUtilisateur($user);

            $repasRes = [];
            foreach ($formData['day'] as $jourIndex => $day) {
                foreach ($day as $key => $repasCommande) {

                    if ($repasCommande !== 'false') {

                        $RepasReserve = new RepasReserve;
                        $RepasReserve->setReservation($Reservation);

                        foreach ($repasArray[$jourIndex] as $key => $repas) {
                            $type = $repas->getTypeRepas()->getType();
                            if ($repasCommande === $type) {
                                $RepasReserve->setRepas($repas);
                            }
                        }
                        $entityManager->persist($RepasReserve);
                        $repasRes[] = $RepasReserve;
                    }
                }
            }

            if (count($repasRes) > 0) {
                $total = $this->calculateTotal($repasRes, $tarifReduit);
                $Reservation->setMontantTotal($total);
                $userInfo = $user->getUserInfo();
                $montantglobal = $this->calculateMontantGlobal($userInfo);
                $userInfo->setMontantGlobal($montantglobal);
                $entityManager->persist($Reservation);
                $entityManager->persist($userInfo);
                $entityManager->flush();
            } else {
                return new Response('Error', Response::HTTP_CONFLICT);
            }
        }


        return $this->render('user_reservation/reservation.html.twig', [
            'controller_name' => 'UserReservationController',
            'tarifReduit' => $tarifReduit
        ]);
    }


    #[Route('/user/reservation/modif/{id}', name: 'user_modif')]
    public function modif(Request $request, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        //Get User Roles
        $user = $this->getUser();
        $roles = $user->getRoles();
        $tarifReduit = false;
        $date = new \DateTime();
        $Reservation = $reservationRepository->find($id);
        $semaine = $Reservation->getSemaine();
        $semaineId = $semaine->getId();
        for ($i = 0; $i < count($roles); $i++) {
            if ($roles[$i] === 'ROLE_STAGIAIRE') {
                $tarifReduit = true;
            }
        }

        $formData = $request->request->all();
        if ($request->isMethod('POST')) {

            $repasArray =  $this->fetchRepas($semaine);

            // Retrieve existing RepasReserve entities associated with the reservation
            $existingRepasReserve =  $Reservation->getRepasReserves();

            // Initialize an array to hold new RepasReserve entities
            $newRepasReserve = [];

            foreach ($formData['day'] as $jourIndex => $day) {
                foreach ($day as $key => $repasCommande) {

                    if ($repasCommande !== 'false') {

                        $foundInExisting = false;

                        // Check if the repasCommande already exists in the existing RepasReserve
                        foreach ($existingRepasReserve as $existingRepas) {
                            if ($repasCommande === $existingRepas) {
                                $foundInExisting = true;
                                $newRepasReserve[] = $existingRepas; // Keep existing RepasReserve
                                break;
                            }
                        }

                        if (!$foundInExisting) {
                            // Create a new RepasReserve entity
                            $RepasReserve = new RepasReserve;
                            $RepasReserve->setReservation($Reservation);

                            foreach ($repasArray[$jourIndex] as $key => $repas) {
                                $type = $repas->getTypeRepas()->getType();
                                if ($repasCommande === $type) {
                                    $RepasReserve->setRepas($repas);
                                }
                            }

                            $entityManager->persist($RepasReserve);
                            $newRepasReserve[] = $RepasReserve;
                        }
                    }
                }
            }

            // Remove existing RepasReserve entities that are not in the new reservation
            foreach ($existingRepasReserve as $existingRepas) {
                if (!in_array($existingRepas, $newRepasReserve, true)) {
                    $entityManager->remove($existingRepas);
                }
            }

            // Update reservation with new RepasReserve entities
            if (count($newRepasReserve) > 0) {
                //flush all repas first
                $entityManager->flush();
                // Update reservation's total amount
                $total = $this->calculateTotal($newRepasReserve, $tarifReduit);
                $Reservation->setMontantTotal($total);
                // Persist changes
                $entityManager->persist($Reservation);
                $entityManager->flush();
                // Update user's global amount
                $userInfo = $user->getUserInfo();
                $montantGlobal = $this->calculateMontantGlobal($user);
                $userInfo->setMontantGlobal($montantGlobal);
                $entityManager->persist($userInfo);
                $entityManager->flush();
            } else {
                return new Response('Error', Response::HTTP_CONFLICT);
            }
        }

        return $this->render('user_reservation/modif.html.twig', [
            'controller_name' => 'UserReservationController',
            'tarifReduit' => $tarifReduit,
            'semaine' => $semaineId
        ]);
    }
    private function fetchRepas($semaineToFetch)
    {
        $repasArray = [];
        foreach ($semaineToFetch->getJourReservation() as $key => $jour) {
            $repasForJour = [];
            $date = $jour->getDateJour();
            $timestamp = $date->getTimestamp();
            $jourIndex = date("l", $timestamp);
            foreach ($jour->getRepas() as $key => $repasToFetch) {
                $repasForJour[] = $repasToFetch;
            }
            $repasArray[$jourIndex] = $repasForJour;
        }
        return $repasArray;
    }
    private function calculateTotal($repasRes, $tarifReduit)
    {
        $total = 0;
        if ($tarifReduit) {
            foreach ($repasRes as $key => $repasRes) {
                $prixReduit = $repasRes->getRepas()->getTypeRepas()->getTarifReduit();
                $total = $total + $prixReduit;
            }
        } else {
            foreach ($repasRes as $key => $repasRes) {
                $prixPlein = $repasRes->getRepas()->getTypeRepas()->getTarifPlein();
                $total = $total + $prixPlein;
            }
        }
        return $total;
    }
    private function calculateMontantGlobal($user)
    {
        $reservations = $user->getReservations();
        $montantGlobal = 0;
        foreach ($reservations as $key => $reservation) {
            $montant = $reservation->getMontantTotal();
            $montantGlobal = $montantGlobal + $montant;
        }
        return $montantGlobal;
    }

    #[Route('/user/reservation/semaineJson', name: 'app_user_reservation_semaineJson')]
    public function reserverJson(SerializerInterface $serializer, JourReservationRepository $jourReservationRepository): JsonResponse
    {
        $date = new \DateTime();
        $curd = date('Y-m-d');

        $jourReservations = $jourReservationRepository->findAll();

        // Initialize an array to store semaine entities
        $semaines = [];

        // Iterate through each JourReservation entity
        foreach ($jourReservations as $jourReservation) {
            // Retrieve the Semaine associated with this JourReservation
            $semaine = $jourReservation->getSemaineReservation();
            //to change
            if ($semaine->getDateFin() > $curd) {
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

    #[Route('/user/reservation/repasJson/{id}', name: 'user_reservation_repasJson')]
    public function repasJson(int $id, SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepository): JsonResponse
    {
        $date = new \DateTime();
        $semaine = $semaineReservationRepository->find($id);
        $serializeSemaine = $serializer->serialize($semaine, 'json', ['groups' => 'semaineResa']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }
    #[Route('/user/reservation/repasModifJson/{idSemaine}', name: 'user_reservation_repasJson_modif')]
    public function repasModifJson(int $idSemaine, SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepository, ReservationRepository $reservationRepository): JsonResponse
    {
        $date = new \DateTime();
        $user = $this->getUser();
        $reservations = $user->getReservations();
        $semaine = $semaineReservationRepository->find($idSemaine);

        $correctResa = null; // Initialize $correctResa variable
        foreach ($reservations as $reservation) {
            if ($reservation->getSemaine() === $semaine) {
                $correctResa = $reservation;
                break;
            }
        }

        // Serialize each object separately
        $serializeSemaine = $serializer->serialize($semaine, 'json', ['groups' => 'semaineResa']);
        $serializeCorrectResa = $serializer->serialize($correctResa, 'json', ['groups' => 'consultation']);

        // Decode serialized data into arrays
        $jsonContentSemaine = json_decode($serializeSemaine, true);
        $jsonContentCorrectResa = json_decode($serializeCorrectResa, true);

        // Return two distinct arrays as part of a single JSON response
        return new JsonResponse([
            'semaine' => $jsonContentSemaine,
            'reservation' => $jsonContentCorrectResa
        ]);
    }
    
}

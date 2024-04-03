<?php

namespace App\Controller;

use App\Entity\RepasReserve;
use App\Entity\Reservation;
use App\Repository\JourReservationRepository;
use App\Repository\ReservationRepository;
use App\Repository\SemaineReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserReservationController extends AbstractController
{
    #[Route('/user/reservation', name: 'app_user_reservation')]
    public function index(Request $request, SemaineReservationRepository $semaineReservationRepository, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
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
            $semaine = $semaineReservationRepository->findOneBy(["numeroSemaine" => $semaineSelect]);
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
            $total = $this->calculateTotal($repasRes, $tarifReduit);
            $Reservation->setMontantTotal($total);
            $entityManager->persist($Reservation);
            $entityManager->flush();
        }


        return $this->render('user_reservation/reservation.html.twig', [
            'controller_name' => 'UserReservationController',
            'tarifReduit' => $tarifReduit
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

    #[Route('/user/reservation/semaineJson', name: 'app_user_reservation_semaineJson')]
    public function reserverJson(SerializerInterface $serializer, JourReservationRepository $jourReservationRepository): Response
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

        $jsonContent = $serializer->serialize($semaines, 'json', ['groups' => 'semaine']);
        return new Response($jsonContent);
    }

    #[Route('/user/reservation/repasJson/{id}', name: 'app_user_reservation_repasJson')]
    public function repasJson(int $id, SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepository): Response
    {
        $date = new \DateTime();
        $semaine = $semaineReservationRepository->findOneBy(['numeroSemaine' => $id]);
        $jsonContent = $serializer->serialize($semaine, 'json', ['groups' => 'reservation']);
        return new Response($jsonContent);
    }
}

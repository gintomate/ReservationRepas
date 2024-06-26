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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserReservationController extends AbstractController
{
    //CREATE TWIG
    #[Route('/user/reservation', name: 'user_reservation')]
    public function reserver(): Response
    {

        $user = $this->getUser();
        //Get User Roles
        $roles = $user->getRoles();
        $tarifReduit = in_array('ROLE_STAGIAIRE', $roles);

        return $this->render('user_reservation/reservation.html.twig', [
            'controller_name' => 'UserReservationController',
            'tarifReduit' => $tarifReduit
        ]);
    }

    //CREATE FORM 
    #[Route('/user/reservation/submit', name: 'user_reservation_submit', methods: ['POST'])]
    public function submitReservation(
        Request $request,
        ValidatorInterface $validator,
        SemaineReservationRepository $semaineReservationRepo,
        ReservationRepository $reservationRepo,
        EntityManagerInterface $em
    ): Response {
        //SET TIMEZONE TO LA REUNION 
        date_default_timezone_set("Indian/Reunion");

        if (!$request->isMethod('POST')) {
            $this->addFlash(
                'error',
                'Methode Invalide.'
            );
            return new Response('Error', Response::HTTP_CONFLICT);
        }
        $dateJour = new \DateTime();
        $user = $this->getUser();

        //Get User Roles
        $roles = $user->getRoles();
        $tarifReduit = in_array('ROLE_STAGIAIRE', $roles);
        $formData = $request->request->all();

        $semaineSelect =  $formData['semaine'];
        $semaine = $semaineReservationRepo->find($semaineSelect);
        $dateLimit = $semaine->getDateLimit();

        //Verifie date validité de la reservation
        if ($dateJour >= $dateLimit) {
            $this->addFlash(
                'error',
                'La date de Réservation est dépassé.'
            );
            return new Response('Error', Response::HTTP_CONFLICT);
        } else {
            $repasArray =  $this->fetchRepas($semaine);
            //verifie que la reservation n'existe pas
            $existingReservation = $reservationRepo->findOneBy([
                'semaine' => $semaine,
                'Utilisateur' => $user
            ]);
            if ($existingReservation) {
                $this->addFlash(
                    'error',
                    'Une réservation pour cette semaine existe déja.'
                );
                return new Response('Error', Response::HTTP_CONFLICT);
            }
            //new Reservation
            $Reservation = new Reservation;
            $Reservation->setSemaine($semaine);
            $Reservation->setUtilisateur($user);
            $repasRes = [];
            //pour chaque jour
            foreach ($formData['day'] as $jourIndex => $day) {
                //pour chaque repas
                foreach ($day as $key => $repasCommande) {
                    //si repas est réservé 
                    if ($repasCommande !== 'false') {
                        $RepasReserve = new RepasReserve;
                        $RepasReserve->setReservation($Reservation);
                        foreach ($repasArray[$jourIndex] as $key => $repas) {
                            $type = $repas->getTypeRepas()->getType();
                            if ($repasCommande === $type) {
                                $RepasReserve->setRepas($repas);
                            }
                        }
                        $em->persist($RepasReserve);
                        $repasRes[] = $RepasReserve;
                    }
                }
            }
            //Si il y a un repas Validate
            if (count($repasRes) > 0) {
                $total = $this->calculateTotal($repasRes, $tarifReduit);
                $Reservation->setMontantTotal($total);
                $userInfo = $user->getUserInfo();
                $violation = $validator->validate($Reservation);
                //Si il ya pas de violation
                if (count($violation) < 1) {
                    $em->persist($Reservation);
                    $montantglobal = $this->calculateMontantGlobal($user);
                    $userInfo->setMontantGlobal($montantglobal);
                    $em->persist($userInfo);
                    $em->flush();
                    $this->addFlash(
                        'success',
                        'Votre Reservation a bien été crée.'
                    );
                    return $this->redirectToRoute('user_consultation');
                } else {
                    $this->addFlash(
                        'error',
                        'Réservation Invalide.'
                    );
                    return new Response('Error', Response::HTTP_CONFLICT);
                }
            } else {
                $this->addFlash(
                    'error',
                    'Erreur dans la réservation.'
                );
                return new Response('Error', Response::HTTP_CONFLICT);
            }
        }
    }


    //UPDATE

    #[Route('/user/reservation/modif/{id}', name: 'user_modif')]
    public function modif(Request $request, EntityManagerInterface $em, Reservation $Reservation, ValidatorInterface $validator): Response
    {
        //Get User
        $user = $this->getUser();
        //Get User Roles

        $roles = $user->getRoles();
        $tarifReduit = in_array('ROLE_STAGIAIRE', $roles);
        if ($Reservation->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'étes pas autorisé à accéder à cette page.');
        }
        $semaine = $Reservation->getSemaine();
        $semaineId = $semaine->getId();

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

                            $em->persist($RepasReserve);
                            $newRepasReserve[] = $RepasReserve;
                        }
                    }
                }
            }
            // Remove existing RepasReserve entities that are not in the new reservation
            foreach ($existingRepasReserve as $existingRepas) {
                if (!in_array($existingRepas, $newRepasReserve, true)) {
                    $em->remove($existingRepas);
                }
            }

            // Update reservation with new RepasReserve entities
            if (count($newRepasReserve) > 0) {
                //flush all repas first
                $em->flush();
                // Update reservation's total amount
                $total = $this->calculateTotal($newRepasReserve, $tarifReduit);
                $Reservation->setMontantTotal($total);
                // Persist changes
                $violation = $validator->validate($Reservation);
                if (count($violation) < 1) {
                    $em->persist($Reservation);
                    $em->flush();
                    // Update user's global amount
                    $userInfo = $user->getUserInfo();
                    $montantGlobal = $this->calculateMontantGlobal($user);
                    $userInfo->setMontantGlobal($montantGlobal);
                    $em->persist($userInfo);
                    $em->flush();
                    $this->addFlash(
                        'success',
                        'Votre Reservation a bien été modifié.'
                    );
                    return $this->redirectToRoute('user_consultation');
                } else {
                    $this->addFlash(
                        'error',
                        'Réservation Invalide.'
                    );
                    return new Response('Error', Response::HTTP_CONFLICT);
                }
            } else {
                $this->addFlash(
                    'error',
                    'Erreur dans la réservation.'
                );
                return new Response('Error', Response::HTTP_CONFLICT);
            }
        }

        return $this->render('user_reservation/modif.html.twig', [
            'controller_name' => 'UserReservationController',
            'tarifReduit' => $tarifReduit,
            'semaine' => $semaineId
        ]);
    }

    //DELETE

    #[Route('/user/reservation/delete/{id}', name: 'user_reservation_delete')]
    public function delete(ReservationRepository $reservationRepo, int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $Reservation = $reservationRepo->find($id);
        if (!$Reservation) {
            throw $this->createNotFoundException('La réservation n\'existe pas.');
        }
        if ($Reservation->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'étes pas autorisé à accéder à cette page.');
        }
        foreach ($Reservation->getRepasReserves() as $repasReserve) {
            $em->remove($repasReserve);
        }
        $em->remove($Reservation);
        $em->flush();
        // MISE A JOUR DU MONTANT GLOBAL
        $userInfo = $user->getUserInfo();
        $montantGlobal = $this->calculateMontantGlobal($user);
        $userInfo->setMontantGlobal($montantGlobal);
        $em->persist($userInfo);
        $em->flush();
        $this->addFlash(
            'success',
            'Votre Réservation a bien été supprimé'
        );
        return $this->redirectToRoute('user_consultation');
    }

    //FONCTION TO RETURN ALL THE REPAS FOR A RESERVATION
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

    //FONCTION TO CALCULATE THE TOTAL OF ALL THE REPASRES
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
    //FONCTION TO CALCULATE THE TOTAL OF ALL RESERVATION
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

    //JSON FOR CREATE

    #[Route('/user/reservation/semaineJson', name: 'user_reservation_semaineJson')]
    public function reserverJson(SerializerInterface $serializer, JourReservationRepository $jourReservationRepo): JsonResponse
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
            if ($semaine->getDateFin() >  $dateJour) {
                if (!in_array($semaine, $semaines, true)) {
                    // Add this Semaine to the array
                    $semaines[] = $semaine;
                }
            }
        }
        //Serialyse in Json
        $serializeSemaine = $serializer->serialize($semaines, 'json', ['groups' => 'semaine']);
        $jsonContent = json_decode($serializeSemaine, true);
        // Sort the array based on 'dateDebut' field
        usort($jsonContent, function ($a, $b) {
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

    //JSON FOR THE MENU
    #[Route('/user/reservation/repasJson/{id}', name: 'user_reservation_repasJson')]
    public function repasJson(int $id, SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepo): JsonResponse
    {
        $semaine = $semaineReservationRepo->find($id);
        $serializeSemaine = $serializer->serialize($semaine, 'json', ['groups' => 'semaineResa']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }

    //JSON FOR MENU MODIF
    #[Route('/user/reservation/repasModifJson/{idSemaine}', name: 'user_reservation_repasJson_modif')]
    public function repasModifJson(int $idSemaine, SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepo): JsonResponse
    {
        $user = $this->getUser();
        $reservations = $user->getReservations();
        $semaine = $semaineReservationRepo->find($idSemaine);

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

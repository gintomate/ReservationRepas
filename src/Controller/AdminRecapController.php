<?php

namespace App\Controller;

use App\Repository\JourReservationRepository;
use App\Repository\PromoRepository;
use App\Repository\ReservationRepository;
use App\Repository\SemaineReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AdminRecapController extends AbstractController
{
    #[Route('admin/recap', name: 'admin_recap')]
    public function recap(): Response
    {
        return $this->render('admin/recap.html.twig', [
            'controller_name' => 'AdminRecapController',
        ]);
    }

    #[Route('admin/recap/SemaineJson', name: 'admin_recap_semaine_json')]
    public function recapSemaineJson(SerializerInterface $serializer, JourReservationRepository $jourReservationRepo, PromoRepository $promoRepo): JsonResponse
    {
        $promoBySection = $promoRepo->createQueryBuilder('p')
            ->groupBy('p.Section')
            ->getQuery()
            ->getResult();
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
        $serializedSection = $serializer->serialize($promoBySection, 'json', ['groups' => 'userInfo']);
        $serializedSemaine = $serializer->serialize($semaines, 'json', ['groups' => 'semaine']);
        $jsonContent = [
            'sections' => json_decode($serializedSection, true),
            'semaines' => json_decode($serializedSemaine, true)
        ];

        return new JsonResponse($jsonContent);
    }


    #[Route('admin/recapJson/{section}/{semaine}', name: 'admin_recap_json')]
    public function recapJson(SerializerInterface $serializer, UserRepository $userRepository, ReservationRepository $reservationRepository, int $semaine, int $section): JsonResponse
    {
        $sectionChoisi = $userRepository
            ->createQueryBuilder('u')
            ->innerJoin('u.userInfo', 'ui')
            ->innerJoin('ui.promo', 'p')
            ->where('p.id = :section ')
            ->setParameter('section', $section)
            ->getQuery()
            ->getResult();

        $semaineChoisi = $reservationRepository
            ->createQueryBuilder('r')
            ->innerJoin('r.semaine', 'sr')
            ->where('sr.id = :semaine ')
            ->setParameter('semaine', $semaine)
            ->getQuery()
            ->getResult();

        $usersWithReservations = [];

        foreach ($sectionChoisi as $user) {
            $userReservations = $user->getReservations();
            $roles = $user->getRoles();
            $tarifReduc = in_array('ROLE_STAGIAIRE', $roles);
            //no reservation at all
            if (count($userReservations) < 1) {
                $usersWithReservations[] = [
                    'user' => $user,
                    'reservation' => null
                ];
            } else {
                foreach ($userReservations as $reservation) {

                    if (in_array($reservation, $semaineChoisi)) {
                        $usersWithReservations[] = [
                            'user' => $user,
                            'reservation' => $reservation,
                            'tarifReduc' => $tarifReduc
                        ];
                        break; // No need to continue checking other reservations for this user
                    } else {
                        $usersWithReservations[] = [
                            'user' => $user,
                            'reservation' => null
                        ];
                    }
                }
            }
        }
        $userSerialised = $serializer->serialize($usersWithReservations, 'json', ['groups' => ['userInfo', 'reservation']]);
        $jsonContent = json_decode($userSerialised, true);

        return new JsonResponse($jsonContent);
    }
}

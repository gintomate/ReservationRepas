<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use App\Repository\SectionRepository;
use App\Repository\SemaineReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AdminRecapController extends AbstractController
{
    #[Route('/recap', name: 'app_admin_recap')]
    public function index(): Response
    {
        return $this->render('admin/recap.html.twig', [
            'controller_name' => 'AdminRecapController',
        ]);
    }

    #[Route('/recapSemaineJson', name: 'recap_semaine_json')]
    public function recapSemaineJson(SerializerInterface $serializer, SectionRepository $sectionRepository, SemaineReservationRepository $semaineReservationRepository): Response
    {
        $section = $sectionRepository->findAll();
        $semaine = $semaineReservationRepository->findAll();
        $serializedSection = $serializer->serialize($section, 'json', ['groups' => 'section']);
        $serializedSemaine = $serializer->serialize($semaine, 'json', ['groups' => 'semaine']);
        $jsonContent = [
            'sections' => json_decode($serializedSection, true),
            'semaines' => json_decode($serializedSemaine, true)
        ];

        return new JsonResponse($jsonContent);
    }


    #[Route('/recapJson/{section}/{semaine}', name: 'app_admin_recap_json')]
    public function recapJson(SerializerInterface $serializer, UserRepository $userRepository, ReservationRepository $reservationRepository, int $semaine, int $section): JsonResponse
    {
        $sectionChoisi = $userRepository
            ->createQueryBuilder('u')
            ->innerJoin('u.userInfo', 'ui')
            ->innerJoin('ui.promo', 'p')
            ->innerJoin('p.Section', 's')
            ->where('s.id = :section ')
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
            $userReservations = $user->getReservations(); // Assuming `getReservations()` is the method to retrieve reservations from UserInfo entity
            foreach ($userReservations as $reservation) {
                // Check if the reservation exists in $semaineChoisi
                if (in_array($reservation, $semaineChoisi)) {
                    $usersWithReservations[] = [
                        'user' => $user,
                        'reservation' => $reservation
                    ];
                    break; // No need to continue checking other reservations for this user
                }
            }
        }

        $userSerialise = $serializer->serialize($usersWithReservations, 'json', ['groups' => ['userInfo', 'reservation']]);
        $jsonContent = [
            'reservation' => json_decode($userSerialise, true)
        ];

        return new JsonResponse($jsonContent);
    }
}

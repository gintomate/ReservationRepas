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
    #[Route('admin/recap', name: 'admin_recap')]
    public function recap(): Response
    {
        return $this->render('admin/recap.html.twig', [
            'controller_name' => 'AdminRecapController',
        ]);
    }

    #[Route('admin/recap/SemaineJson', name: 'admin_recap_semaine_json')]
    public function recapSemaineJson(SerializerInterface $serializer, SectionRepository $sectionRepository, SemaineReservationRepository $semaineReservationRepository): JsonResponse
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


    #[Route('admin/recapJson/{section}/{semaine}', name: 'admin_recap_json')]
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
            $userReservations = $user->getReservations();
            $roles = $user->getRoles();
            $tarifReduc = false;
            for ($i = 0; $i < count($roles); $i++) {
                if ($roles[$i] === 'ROLE_STAGIAIRE') {
                    $tarifReduc = true;
                }
            }
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
        $userSerialise = $serializer->serialize($usersWithReservations, 'json', ['groups' => ['userInfo', 'reservation']]);
        $jsonContent = json_decode($userSerialise, true);

        return new JsonResponse($jsonContent);
    }
}

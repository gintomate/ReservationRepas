<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use App\Repository\SemaineReservationRepository;
use App\Repository\UserRepository;
use DateTime;
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
    public function recapSemaineJson(SerializerInterface $serializer, SemaineReservationRepository $semaineReservationRepo): JsonResponse
    {
        $date = new DateTime();
        $semaine = $semaineReservationRepo->findAll();
        $serializedSemaine = $serializer->serialize($semaine, 'json', ['groups' => 'semaine']);
        $jsonContent =  json_decode($serializedSemaine, true);

        return new JsonResponse($jsonContent);
    }
    #[Route('/delegue/recapJson/{semaine}', name: 'delegue_recap_json')]
    public function recapJson(SerializerInterface $serializer, int $semaine, UserRepository $userRepo, ReservationRepository $reservationRepo): JsonResponse
    {
        $user = $this->getUser();
        $promo = $user->getUserInfo()->getPromo()->getId();


        $sectionChoisi = $userRepo
            ->createQueryBuilder('u')
            ->innerJoin('u.userInfo', 'ui')
            ->innerJoin('ui.promo', 'p')
            ->where('p.id = :promo ')
            ->setParameter('promo', $promo)
            ->getQuery()
            ->getResult();

        $semaineChoisi = $reservationRepo
            ->createQueryBuilder('r')
            ->innerJoin('r.semaine', 'sr')
            ->where('sr.id = :semaine ')
            ->setParameter('semaine', $semaine)
            ->getQuery()
            ->getResult();

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

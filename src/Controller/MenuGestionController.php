<?php

namespace App\Controller;

use App\Entity\JourReservation;
use App\Entity\Repas;
use App\Repository\SemaineReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MenuGestionController extends AbstractController
{
    #[Route('/menu/gestion', name: 'app_menu_gestion')]
    public function index(): Response
    {
        return $this->render('menu_gestion/index.html.twig', [
            'controller_name' => 'MenuGestionController',
        ]);
    }

    #[Route('/menu/creer', name: 'app_menu_creer')]
    public function creerMenu(Request $request, SemaineReservationRepository $semaineReservationRepository, EntityManagerInterface $entityManager): Response
    {
        $formData = $request->request->all();
        if ($request->isMethod('POST')) {
            $semaineSelect =  $formData['semaine'];
            $semaine = $semaineReservationRepository->find($semaineSelect);
            $dateDebut = $semaine->getDateDebut();
            $i = 0;
            foreach ($formData['day'] as $key => $day) {

                if ($day['ferie'] === 'true') {
                    $jourReservation = new JourReservation;
                    $jourReservation->setFerie(true);
                    $jourReservation->setDateJour($dateDebut);
                    $jourReservation->setSemaineReservation($semaine);
                    $i++;
                } else {
                    $jourReservation = new JourReservation;
                    $jourReservation->setFerie(false);
                    $jourReservation->setDateJour($dateDebut);
                    $jourReservation->setSemaineReservation($semaine);
                    $i++;

                    $repas = new Repas;
                    if ($day['petit_dejeuner']) {
                        $repas->setJourReservation($jourReservation);
                        $repas->setDescription($day['petit_dejeuner']);
                        $repas->setTypeRepas('petitDejeuner');
                    }
                };
                dump($dateDebut->modify('+1 day'));
            }
        }
        return $this->render('menu_gestion/creer.html.twig');
    }
    private function validateFormData(array $data): array
    {
    }

    #[Route('/menu/creer/get', name: 'app_menu_gestion_json')]
    public function creerJson(SemaineReservationRepository $semaineReservationRepository, SerializerInterface $serializer): Response
    {
        $semaine = $semaineReservationRepository->findAll();
        $jsonContent = $serializer->serialize($semaine, 'json');
        return new Response($jsonContent);
    }
}

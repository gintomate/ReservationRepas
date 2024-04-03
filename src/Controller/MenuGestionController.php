<?php

namespace App\Controller;

use App\Entity\JourReservation;
use App\Entity\Repas;
use App\Repository\SemaineReservationRepository;
use App\Repository\TypeRepasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function creerMenu(Request $request, ValidatorInterface $validator, SemaineReservationRepository $semaineReservationRepository, EntityManagerInterface $entityManager, TypeRepasRepository $typeRepasRepository): Response
    {
        $formData = $request->request->all();


        if ($request->isMethod('POST')) {
            $semaineSelect =  $formData['semaine'];
            $semaine = $semaineReservationRepository->findOneBy(["numeroSemaine" => $semaineSelect]);
            $dateDebut = $semaine->getDateDebut();
            $formValid = true;
            foreach ($formData['day'] as $key => $day) {
                //clone currentDate because $dateDebut change even after persist
                $currentDate = clone $dateDebut;
                if ($day['ferie'] === 'true') {
                    $jourReservation = new JourReservation;
                    $jourReservation->setFerie(true);
                    $jourReservation->setDateJour($currentDate);
                    $jourReservation->setSemaineReservation($semaine);
                    $entityManager->persist($jourReservation);
                } else {
                    $jourReservation = new JourReservation;
                    $jourReservation->setFerie(false);
                    $jourReservation->setDateJour($currentDate);
                    $jourReservation->setSemaineReservation($semaine);
                    $entityManager->persist($jourReservation);
                    foreach (['petit_dejeuner', 'dejeuner_a', 'dejeuner_b', 'diner'] as $mealType) {
                        if (isset($day[$mealType])) {
                            $repas = new Repas;
                            $typeRepas = $typeRepasRepository->findOneBy(['type' => $mealType]);

                            $repas->setJourReservation($jourReservation);
                            $repas->setDescription($day[$mealType]);
                            $repas->setTypeRepas($typeRepas);

                            $errors = $validator->validate($repas);

                            if (count($errors) > 0) {
                                $this->addFlash(
                                    'error',
                                    'Tous les champs doivent être remplis.'
                                );
                                $formValid = false;
                                break 2; // Exit both inner and outer loops
                            }
                            $entityManager->persist($repas);
                        }
                    }
                }
                $dateDebut->modify('+ 1 day');
            }
            if ($formValid === true) {

                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'Le Menu a bien était bien enregistré.'
                );
                // return $this->redirectToRoute('app_accueil');
            }
        }

        return $this->render('menu_gestion/creer.html.twig');
    }

    #[Route('/menu/creer/get', name: 'app_menu_gestion_json')]
    public function creerJson(SemaineReservationRepository $semaineReservationRepository, SerializerInterface $serializer): Response
    {
        $semaine = $semaineReservationRepository->findAll();
        $jsonContent = $serializer->serialize($semaine, 'json', ['groups' => 'semaine']);
        return new Response($jsonContent);
    }
}

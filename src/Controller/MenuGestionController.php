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
    #[Route('/admin/menu', name: 'admin_menu')]
    public function index(): Response
    {
        return $this->render('menu_gestion/index.html.twig', [
            'controller_name' => 'MenuGestionController',
        ]);
    }

    #[Route('admin/menu/creer', name: 'admin_menu_creer')]
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
                $jourReservation = new JourReservation;
                $jourReservation->setDateJour($currentDate);
                $jourReservation->setSemaineReservation($semaine);

                if ($day['ferie'] === 'true') {
                    $jourReservation->setFerie(true);
                } else {
                    $jourReservation->setFerie(false);
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
                $entityManager->persist($jourReservation);
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

    #[Route('admin/menu/creerJson', name: 'admin_menu_creer_json')]
    public function creerJson(SemaineReservationRepository $semaineReservationRepository, SerializerInterface $serializer): Response
    {
        $semaine = $semaineReservationRepository->findAll();
        $jsonContent = $serializer->serialize($semaine, 'json', ['groups' => 'semaine']);
        return new Response($jsonContent);
    }
}

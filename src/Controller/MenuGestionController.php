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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
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
        $errors = '';
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


                    if (isset($day['petit_dejeuner'])) {
                        $repas = new Repas;
                        $typeRepas = $typeRepasRepository->findOneBy(['type' => 'petit_déjeuner']);

                        $repas->setJourReservation($jourReservation);
                        $repas->setDescription($day['petit_dejeuner']);
                        $repas->setTypeRepas($typeRepas);
                        
                        $errors = $validator->validate($repas);

                        if (count($errors) > 0) {

                            break;
                        }
                        $entityManager->persist($repas);

                    }
                    if (isset($day['dejeuner_a'])) {
                        $repas = new Repas;
                        $typeRepas = $typeRepasRepository->findOneBy(['type' => 'dejeuner_a']);

                        $repas->setJourReservation($jourReservation);
                        $repas->setDescription($day['dejeuner_a']);
                        $repas->setTypeRepas($typeRepas);
                        $entityManager->persist($repas);
                    }
                    if (isset($day['dejeuner_b'])) {
                        $repas = new Repas;
                        $typeRepas = $typeRepasRepository->findOneBy(['type' => 'dejeuner_b']);

                        $repas->setJourReservation($jourReservation);
                        $repas->setDescription($day['dejeuner_b']);
                        $repas->setTypeRepas($typeRepas);
                        $entityManager->persist($repas);
                    }
                    if (isset($day['diner'])) {
                        $repas = new Repas;
                        $typeRepas = $typeRepasRepository->findOneBy(['type' => 'diner']);

                        $repas->setJourReservation($jourReservation);
                        $repas->setDescription($day['diner']);
                        $repas->setTypeRepas($typeRepas);
                        $entityManager->persist($repas);
                    }
                };

                dump($dateDebut->modify('+1 day'));
            }
        }

        return $this->render('menu_gestion/creer.html.twig', ['errors' =>$errors]);
    }

    #[Route('/menu/creer/get', name: 'app_menu_gestion_json')]
    public function creerJson(SemaineReservationRepository $semaineReservationRepository, SerializerInterface $serializer): Response
    {
        $semaine = $semaineReservationRepository->findAll();
        $jsonContent = $serializer->serialize($semaine, 'json');
        return new Response($jsonContent);
    }
}

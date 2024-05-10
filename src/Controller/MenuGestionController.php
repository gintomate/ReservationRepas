<?php

namespace App\Controller;

use App\Entity\JourReservation;
use App\Entity\Repas;
use App\Repository\JourReservationRepository;
use App\Repository\SemaineReservationRepository;
use App\Repository\TypeRepasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class MenuGestionController extends AbstractController
{
    // Index

    #[Route('/admin/menu', name: 'admin_menu')]
    public function index(): Response
    {
        return $this->render('menu_gestion/index.html.twig', [
            'controller_name' => 'MenuGestionController',
        ]);
    }

    //CREATE

    #[Route('admin/menu/creer', name: 'admin_menu_creer')]
    public function creerMenu(
        Request $request,
        ValidatorInterface $validator,
        SemaineReservationRepository $semaineReservationRepo,
        EntityManagerInterface $em,
        TypeRepasRepository $typeRepasRepo,
        JourReservationRepository $jourReservationRepo
    ): Response {
        $formData = $request->request->all();

        if ($request->isMethod('POST')) {
            $semaineSelect =  $formData['semaine'];
            $semaine = $semaineReservationRepo->find($semaineSelect);
            $dateDebut = $semaine->getDateDebut();
            $formValid = true;

            //Check existing Menu

            $existingMenu = $jourReservationRepo->findOneBy([
                'dateJour' => $dateDebut
            ]);

            if ($existingMenu) {
                $this->addFlash(
                    'error',
                    'Un Menu pour cette semaine existe déja.'
                );
                return new Response('Une réservation existe déja', Response::HTTP_CONFLICT);
            } else {
                foreach ($formData['day'] as $key => $day) {
                    //clone currentDate because $dateDebut change even after persist
                    $currentDate = clone $dateDebut;
                    $jourReservation = new JourReservation;
                    $jourReservation->setDateJour($currentDate);
                    $jourReservation->setSemaineReservation($semaine);
                    //If ferie count as 1 and go
                    if ($day['ferie'] === 'true') {
                        $jourReservation->setFerie(true);
                    } else {
                        $jourReservation->setFerie(false);
                        foreach (['petit_déjeuner', 'déjeuner_a', 'déjeuner_b', 'diner'] as $mealType) {
                            if (isset($day[$mealType])) {
                                $repas = new Repas;
                                $typeRepas = $typeRepasRepo->findOneBy(['type' => $mealType]);
                                $repas->setJourReservation($jourReservation);
                                $repas->setDescription($day[$mealType]);
                                $repas->setTypeRepas($typeRepas);
                                $errors = $validator->validate($repas);

                                if (count($errors) > 0) {
                                    $formValid = false;
                                    break 2; // Exit both inner and outer loops
                                }
                                $em->persist($repas);
                            }
                        }
                    }
                    $em->persist($jourReservation);
                    $dateDebut->modify('+ 1 day');
                }
                //verify if all is good
                if ($formValid === true) {
                    $em->flush();
                    $this->addFlash(
                        'success',
                        'Le Menu a bien était bien enregistré.'
                    );
                    return $this->redirectToRoute('admin_consultation');
                } else {
                    $this->addFlash(
                        'error',
                        'Erreur dans la création du Menu.'
                    );
                    return new Response('Erreur dans la création du Menu.', Response::HTTP_CONFLICT);
                }
            }
        }

        return $this->render('menu_gestion/creer.html.twig');
    }

    //UPDATE

    #[Route('admin/menu/modif/{idSemaine}', name: 'admin_menu_modif')]
    public function modifMenu(Request $request, int $idSemaine, ValidatorInterface $validator, SemaineReservationRepository $semaineReservationRepo, EntityManagerInterface $em, TypeRepasRepository $typeRepasRepo): Response
    {
        $formData = $request->request->all();
        $semaine = $semaineReservationRepo->find($idSemaine);


        if ($request->isMethod('POST')) {
            $jourReservation = $semaine->getJourReservation();
            $formValid = true;

            // Get all jour from a semaine
            $jourArray = [];
            foreach ($jourReservation as $key => $jour) {
                $datejour = $jour->getDateJour();
                $timestamp = $datejour->getTimestamp();
                $jourIndex = date("l",  $timestamp);
                $jourArray[$jourIndex] =  $jour;
            }
            $jourArrayKeys = array_keys($jourArray);
            foreach ($formData["day"] as $day => $dayData) {
                if (in_array($day, $jourArrayKeys)) {
                    $jourEntity = $jourArray[$day];
                    if ($dayData["ferie"] === 'true') {
                        $repasEntity = $jourEntity->getRepas();
                        foreach ($repasEntity as $key => $repa) {
                            $em->remove($repa);
                        }
                        $jourEntity->setFerie(true);
                    } else {
                        $jourEntity->setFerie(false);
                        $repasEntity = $jourEntity->getRepas();
                        foreach ($repasEntity as $key => $repa) {
                            $em->remove($repa);
                        }
                        foreach (['petit_dejeuner', 'dejeuner_a', 'dejeuner_b', 'diner'] as $mealType) {

                            $dayDataKeys = array_keys($dayData);
                            if (in_array($mealType,  $dayDataKeys)) {
                                $repas = new Repas;
                                $typeRepas = $typeRepasRepo->findOneBy(['type' => $mealType]);
                                $repas->setJourReservation($jourEntity);
                                $repas->setDescription($dayData[$mealType]);
                                $repas->setTypeRepas($typeRepas);
                                $errors = $validator->validate($repas);

                                if (count($errors) > 0) {
                                    $formValid = false;
                                    break 2; // Exit both inner and outer loops
                                }
                                $em->persist($repas);
                            }
                        }
                    }
                    $errors = $validator->validate($jourEntity);
                    if (count($errors) > 0) {
                        $formValid = false;
                        break; // Exit both inner and outer loops
                    }
                    $em->persist($jourEntity);
                }
            }
            //verify if all is good
            if ($formValid === true) {
                $em->flush();
                $this->addFlash(
                    'success',
                    'Le Menu a bien était bien modifié.'
                );
                return $this->redirectToRoute('admin_consultation');
            } else {
                $this->addFlash(
                    'error',
                    'Erreur dans la création du Menu.'
                );
                return new Response('Erreur dans la création du Menu.', Response::HTTP_CONFLICT);
            }
        }

        return $this->render('menu_gestion/modif.html.twig', [
            'semaine' => $semaine,
        ]);
    }

    //DELETE

    #[Route('/admin/menu/delete/{id}', name: 'admin_menu_delete')]
    public function delete(SemaineReservationRepository $semaineRepo, int $id, EntityManagerInterface $em): Response
    {
        $semaine = $semaineRepo->find($id);
        $numeroSemaine = $semaine->getNumeroSemaine();
        $jourReservation = $semaine->getJourReservation();
        if (!$jourReservation) {
            throw $this->createNotFoundException('Le Menu n\'existe pas.');
        }
        foreach ($jourReservation as $key => $jour) {
            $repasJour = $jour->getRepas();

            foreach ($repasJour as $key => $repas) {
                $em->remove($repas);
            }
            $em->remove($jour);
        }
        $em->flush();
        $this->addFlash(
            'success',
            'Le Menu de la semaine ' . $numeroSemaine . ' a bien été supprimé.'
        );
        return $this->redirectToRoute('admin_consultation');
    }

    //JSON FOR CREATE

    #[Route('admin/menu/creerJson', name: 'admin_menu_creer_json')]
    public function creerJson(SemaineReservationRepository $semaineReservationRepo, SerializerInterface $serializer): JsonResponse
    {
        date_default_timezone_set("Indian/Reunion");

        $dateJour = new \DateTime();

        $semaineWithoutMenu = $semaineReservationRepo->findWithoutJourReservation();
        $semaine = [];
        foreach ($semaineWithoutMenu as $key => $semaineWithout) {
            if ($semaineWithout->getDateFin() > $dateJour) {
                $semaine[] = $semaineWithout;
            }
        }

        $serializeSemaine = $serializer->serialize($semaine, 'json', ['groups' => 'semaine']);
        $jsonContent = json_decode($serializeSemaine, true);
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
}

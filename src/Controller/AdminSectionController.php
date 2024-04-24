<?php

namespace App\Controller;

use App\Entity\Promo;
use App\Entity\Section;
use App\Form\PromoType;
use App\Form\SectionType;
use App\Repository\PromoRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface as SerializerInterface;

class AdminSectionController extends AbstractController
{
    //Section All
    #[Route('/admin/section', name: 'admin_section')]
    public function sectionGestion(SectionRepository $sectionRepo): Response
    {
        $sections = $sectionRepo->findAll();

        return $this->render('admin_section/section.html.twig', [
            'controller_name' => 'AdminSectionController',
            'sections' => $sections,
        ]);
    }

    //Section Create

    #[Route('/admin/section/new', name: 'admin_section_new')]
    public function createSection(Request $request, EntityManagerInterface $em): Response
    {
        $section = new Section;
        $form = $this->createForm(SectionType::class, $section);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abreviation = $form->get('abreviation')->getData();
            $majAbreviation =  strtoupper($abreviation);
            $section->setAbreviation($majAbreviation);
            $em->persist($section);
            $em->flush();
            $this->addFlash(
                'success',
                'La section' . $section->getAbreviation() . 'a bien été crée.'
            );
            return $this->redirectToRoute('admin_section');
        }

        return $this->render('admin_section/newSection.html.twig', [
            'controller_name' => 'AdminSectionController',
            'form' => $form,
        ]);
    }


    //Section Solo READ

    #[Route('/admin/section/{id}', name: 'admin_section_solo')]
    public function sectionGestionSolo(SectionRepository $sectionRepo, int $id): Response
    {
        $section = $sectionRepo->find($id);

        return $this->render('admin_section/solo.html.twig', [
            'controller_name' => 'AdminSectionController',
            'section' => $section,
        ]);
    }

    //Section Update

    #[Route('/admin/section/{id}/update/', name: 'admin_section_update')]
    public function updateSection(Request $request, EntityManagerInterface $em, SectionRepository $sectionRepo, int $id): Response
    {
        $section = $sectionRepo->find($id);
        $form = $this->createForm(SectionType::class, $section);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abreviation = $form->get('abreviation')->getData();
            $majAbreviation =  strtoupper($abreviation);
            $section->setAbreviation($majAbreviation);
            $em->persist($section);
            $em->flush();
            $this->addFlash(
                'success',
                'La section' . $section->getAbreviation() . ' a bien été modifié.'
            );
            return $this->redirectToRoute('admin_section');
        }

        return $this->render('admin_section/newSection.html.twig', [
            'controller_name' => 'AdminSectionController',
            'form' => $form,
        ]);
    }

    //PROMO CREATE

    #[Route('/admin/promo/new/{id}', name: 'admin_promo_new')]
    public function newPromo(Request $request, EntityManagerInterface $em, int $id, SectionRepository $sectionRepo): Response
    {
        $Promo = new Promo;
        $form = $this->createForm(PromoType::class, $Promo);
        $section = $sectionRepo->find($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Promo->setSection($section);
            $em->persist($Promo);
            $em->flush();
            $this->addFlash(
                'success',
                'La promo' . $Promo->getNomPromo() . ' a bien été crée.'
            );
            return $this->redirectToRoute('admin_section_solo', ['id' => $section->getId()]);
        }

        return $this->render('admin_section/newPromo.html.twig', [
            'controller_name' => 'AdminSectionController',
            'form' => $form,
        ]);
    }
    //PROMO Update

    #[Route('/admin/promo/update/{id}', name: 'admin_promo_update')]
    public function upadtePromo(Request $request, EntityManagerInterface $em, int $id, PromoRepository $promoRepo): Response
    {
        $promo = $promoRepo->find($id);
        $section = $promo->getSection();
        $form = $this->createForm(PromoType::class, $promo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($promo);
            $em->flush();
            $this->addFlash(
                'success',
                'La promo' . $promo->getNomPromo() . ' a bien été modifié.'
            );
            return $this->redirectToRoute('admin_section_solo', ['id' => $section->getId()]);
        }

        return $this->render('admin_section/newPromo.html.twig', [
            'controller_name' => 'AdminSectionController',
            'form' => $form,
        ]);
    }

    //JSON For Section Solo List

    #[Route('/admin/section/promoJson/{idSection}', name: 'admin_section_promoJson')]
    public function sectionGestionPromoJson(SectionRepository $sectionRepo, int $idSection, SerializerInterface $serializer): JsonResponse

    {
        $section = $sectionRepo->find($idSection);
        $promo = $section->getPromos();

        $serializeSemaine = $serializer->serialize($promo, 'json', ['groups' => 'userInfo']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }

    //JSON For Section Solo

    #[Route('/admin/sectionJson/{idPromo}', name: 'admin_section_Json')]
    public function sectionGestionSemaineJson(PromoRepository $promoRepository, int $idPromo, SerializerInterface $serializer): JsonResponse

    {
        $promo = $promoRepository->find($idPromo);
        $userInfo = $promo->getUserInfos();
        $users = [];

        foreach ($userInfo as $key => $user) {
            $users[] = $user->getUser();
        }

        $serializeSemaine = $serializer->serialize($users, 'json', ['groups' => 'secureUserInfo']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }
}

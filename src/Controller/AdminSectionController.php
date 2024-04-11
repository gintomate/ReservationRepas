<?php

namespace App\Controller;

use App\Repository\PromoRepository;
use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface as SerializerInterface;

class AdminSectionController extends AbstractController
{

    #[Route('/admin/section', name: 'admin_section')]
    public function sectionGestion(SectionRepository $sectionRepository): Response
    {
        $sections = $sectionRepository->findAll();

        return $this->render('admin_section/section.html.twig', [
            'controller_name' => 'AdminSectionController',
            'sections' => $sections,
        ]);
    }
    #[Route('/admin/section/{id}', name: 'admin_section_solo')]
    public function sectionGestionSolo(SectionRepository $sectionRepository, int $id): Response
    {
        $section = $sectionRepository->find($id);

        return $this->render('admin_section/solo.html.twig', [
            'controller_name' => 'AdminSectionController',
            'section' => $section,
        ]);
    }
    #[Route('/admin/section/promoJson/{idSection}', name: 'admin_section_promoJson')]
    public function sectionGestionPromoJson(SectionRepository $sectionRepository, int $idSection, SerializerInterface $serializer): JsonResponse

    {
        $section = $sectionRepository->find($idSection);
        $promo = $section->getPromos();

        $serializeSemaine = $serializer->serialize($promo, 'json', ['groups' => 'userInfo']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }

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

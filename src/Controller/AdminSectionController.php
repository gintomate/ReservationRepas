<?php

namespace App\Controller;

use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminSectionController extends AbstractController
{

    #[Route('/admin/section', name: 'app_admin_section')]
    public function sectionGestion(SectionRepository $sectionRepository): Response
    {
        $sections = $sectionRepository->findAll();

        return $this->render('admin_section/index.html.twig', [
            'controller_name' => 'AdminSectionController',
            'sections' => $sections,
        ]);
    }
}

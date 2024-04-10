<?php

namespace App\Controller;

use App\Form\UpdateUserType;
use App\Repository\PromoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AdminGestionController extends AbstractController
{
    #[Route('/admin/gestion', name: 'admin_gestion')]
    public function index(): Response
    {
        return $this->render('admin_gestion/index.html.twig', [
            'controller_name' => 'AdminGestionController',
        ]);
    }

    #[Route('/admin/gestion/user', name: 'admin_gestion_user')]
    public function consultUser(): Response
    {
        return $this->render('admin_gestion/consultUser.html.twig', [
            'controller_name' => 'AdminGestionController',
        ]);
    }

    #[Route('/admin/gestion/user/promoJson', name: 'admin_gestion_user_promoJson')]
    public function consultPromoJson(SerializerInterface $serializer, PromoRepository $promoRepository): JsonResponse
    {
        $promo = $promoRepository->findAll();

        $serializeSemaine = $serializer->serialize($promo, 'json', ['groups' => 'userInfo']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }
    #[Route('/admin/gestion/user/listJson/{promo}', name: 'admin_gestion_user_listJson')]
    public function consultListJson(SerializerInterface $serializer, PromoRepository $promoRepository, int $promo, UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository
            ->createQueryBuilder('u')
            ->innerJoin('u.userInfo', 'ui')
            ->innerJoin('ui.promo', 'p')
            ->where('p.id = :promo ')
            ->setParameter('promo', $promo)
            ->getQuery()
            ->getResult();

        $serializeSemaine = $serializer->serialize($users, 'json', ['groups' => 'userInfo']);
        $jsonContent = json_decode($serializeSemaine, true);
        return new JsonResponse($jsonContent);
    }
    #[Route('/admin/gestion/userJson/{id}', name: 'admin_gestion_userJson')]
    public function consultUserJson(int $id, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $user = $userRepository->find($id);
        $serializeUser = $serializer->serialize($user, 'json', ['groups' => 'secureUserInfo']);
        $jsonContent = json_decode($serializeUser, true);
        return new JsonResponse($jsonContent);
    }
    #[Route('/admin/gestion/modif/{id}', name: 'admin_gestion_modif_user')]
    public function modifUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, int $id): Response
    {
        $user = $userRepository->find($id);

        $form = $this->createForm(UpdateUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $statut = $form->get('statut')->getData();

            switch ($statut) {
                case 'Stagiaire':
                    $roles = ['ROLE_STAGIAIRE'];
                    break;
                case 'Personnel':
                    $roles = ['ROLE_PERSONNEL'];
                    break;
                case 'Cuisinier':
                    $roles = ['ROLE_CUISINIER'];
                    break;
                case 'Externe':
                    $roles = ['ROLE_EXTERNE'];
                    break;
                default:
                    throw new \Exception("Invalid statut: $statut");
            }
            $delegue = $form->get('delegue')->getData();
            if ($delegue === true) {
                $roles[] = 'ROLE_DELEGUE';
            }
            $user->setRoles($roles);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_gestion/changeUser.html.twig', [
            'form' => $form
        ]);
    }
}

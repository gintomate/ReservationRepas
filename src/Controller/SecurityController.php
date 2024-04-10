<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use function PHPUnit\Framework\throwException;

class SecurityController extends AbstractController
{
    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }
    #[Route('admin/inscription', name: 'security_registration')]
    public function registration(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
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
            $user->setPassword($hashedPassword);
            $user->setRoles($roles);
            $user->getUserInfo()->setMontantGlobal(0);
            $entityManager->persist($user);

            $entityManager->flush();
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/connection', name: 'login')]

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    #[Route('/deconnection', name: 'logout')]

    public function logout()
    {
    }
}

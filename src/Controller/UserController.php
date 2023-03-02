<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class UserController extends AbstractController
{
    private UserService $userManager;

    private UserRepository $userRepository;

    public function __construct(UserService $userManager, UserRepository $userRepository)
    {
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
    }

    #[Route('/GetUsersJson')]
    public function getUsersJson(): JsonResponse
    {
        $users = $this->userRepository->getAll();

        return $this->json(['users' => $users], 200);
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function inscription(): Response
    {
        $user = new User();

        $form = $this->userManager->inscriptionFormulaire($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->inscription($user);

            return $this->redirectToRoute('app_connexion');
        }

        return $this->renderForm('/user/inscription.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/utilisateur/getUsers', name: 'app_get_utilisateur', methods: 'GET')]
    public function getSomeUser(Request $request): User
    {
        $value = $this->userManager->isXmlHttpRequest($request);
        return $this->userManager->getSomeUsers($value);
    }

    #[Route('/admin/utilisateur/consulter', name: 'app_consulter_utilisateur', methods: 'GET')]
    public function consulterListeUser(Request $request): Response
    {
        $template = $this->userManager->isXmlHttpRequest($request) ? '_list.html.twig' : 'consulter.html.twig';
        return $this->renderForm('/user/'.$template, [
            'users' => $this->userManager->getAllASC(),
        ]);
    }

    #[Route('/admin/utilisateur/updateStatus/{pseudo}', name: 'app_updateStatus_utilisateur', methods: 'PUT')]
    public function updateStatus(User $utilisateur): Response
    {
        $this->userManager->updateStatus($utilisateur);
        return new Response(200);
    }

    #[Route('/admin/utilisateur/supprimer/{pseudo}', name: 'app_supprimer_utilisateur')]
    public function supprimerUtilisateur(User $utilisateur): Response
    {
        $this->userManager->supprimer($utilisateur);
        return new Response(200);
    }

    #[Route('/admin/utilisateur/modifier/{pseudo}', name: 'app_modifier_admin_utilisateur')]
    public function modifierAdminUtilisateur(User $utilisateur): Response
    {
        $form = $this->userManager->modifierAdminFormulaire($utilisateur);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->modifierAdmin($utilisateur, $form);
            return $this->redirectToRoute('app_consulter_utilisateur');
        }

        return $this->renderForm('/user/modifier_admin.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/utilisateur/consulter/', name: 'app_consulter_un_utilisateur')]
    public function afficherUn(): Response
    {
        $user = $this->userManager->getConnectedUser($this->getUser()->getUserIdentifier());
        return $this->render('/user/consulter_un.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/verifier', name: 'app_verifier_email')]
    public function verifierEmail(): RedirectResponse
    {
        try {
            $this->userManager->verifyEmail();
        } catch (NotFoundHttpException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('app_inscription');
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_inscription');
        }
        $this->addFlash('success', 'Votre compte est désormais activé.');
        return $this->redirectToRoute('app_connexion');
    }

    #[Route('/verifier/renvoyer/{pseudo}', name: "app_verifier_renvoyer_email")]
    public function renvoyerVerifierEmail(User $user): Response
    {
        return $this->render('/user/renvoyer_verifier_email.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/verifier/renvoyer/formulaire/{pseudo}', name: 'app_verifier_renvoyer_email_formulaire')]
    public function formulaireRenvoyerVerifierEmail(User $user): RedirectResponse
    {
        $this->userManager->envoyerVerifierEmail($user);

        return $this->redirectToRoute('app_connexion');
    }

    #[Route('/connexion', name: 'app_connexion')]
    public function connexion(): Response
    {
        return $this->render('/user/connexion.html.twig', [
            'error' => $this->userManager->connexion(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/utilisateur/deconnexion', name: 'app_deconnexion')]
    public function deconnexion()
    {
        throw new Exception('ne doit jamais être atteint.');
    }
}

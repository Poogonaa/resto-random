<?php

namespace App\Service;

use App\Entity\User;
use App\Form\InscriptionType;
use App\Form\UserEditAdminType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class UserService
{
    private Request $request;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private FormFactoryInterface $formFactory;
    private UserPasswordHasherInterface $hasher;
    private AuthenticationUtils $authenticationUtils;
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private TransportInterface $mailer;

    public function __construct(
        RequestStack $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        FormFactoryInterface $formFactory,
        UserPasswordHasherInterface $hasher,
        AuthenticationUtils $authenticationUtils,
        VerifyEmailHelperInterface $verifyEmailHelper,
        TransportInterface $mailer
    ) {
        $this->request = $request->getCurrentRequest();
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->formFactory = $formFactory;
        $this->hasher = $hasher;
        $this->authenticationUtils = $authenticationUtils;
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
    }

    public function inscriptionFormulaire(User $user): FormInterface
    {
        $form = $this->formFactory->create(InscriptionType::class, $user);

        $form->handleRequest($this->request);

        return $form;
    }

    public function inscription(User $user): void
    {
        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $user->setActive(true);
        $user->setPoint(0);

        $this->envoyerVerifierEmail($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function envoyerVerifierEmail(User $user)
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verifier_email',
            $user->getPseudo(),
            $user->getMail(),
            ['pseudo' => $user->getPseudo()]
        );

        $email = (new TemplatedEmail())
            ->from(new Address('active@resto-random.com', 'Resto-random mail bot'))
            ->to($user->getMail())
            ->subject('Activé votre compte')
            ->htmlTemplate('user/email.html.twig')
            ->context([
                'lien' => $signatureComponents->getSignedUrl(),
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function connexion(): ?AuthenticationException
    {
        return $this->authenticationUtils->getLastAuthenticationError();
    }

    public function getSomeUsers($value): User
    {
        return $this->userRepository->findOneBySomeFiled($value);
    }
    public function getAllASC(): array
    {
        return $this->userRepository->findBy([], ['pseudo' => 'ASC']);
    }

    public function updateStatus(User $user)
    {
        $this->userRepository->changeActiveStatus($user);
    }

    public function supprimer($utilisateur)
    {
        $this->entityManager->remove($utilisateur);
        $this->entityManager->flush();
    }

    public function modifierAdminFormulaire(User $user): FormInterface
    {
        $form = $this->formFactory->create(UserEditAdminType::class, $user);
        $form->handleRequest($this->request);
        return $form;
    }

    public function modifierAdmin(User $user, FormInterface $form)
    {
        if ($form['admin']->getData() == true) {
            $user->setRoles(['ROLE_ADMIN']);
        } else {
            $user->setRoles([]);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function getConnectedUser(String $pseudo): ?User
    {
        return $this->userRepository->findOneBy(['pseudo' => $pseudo]);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     * @throws NotFoundHttpException
     */
    public function verifyEmail()
    {
        $user = $this->userRepository->find($this->request->get('pseudo'));
        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non trouvé.', null);
        }
        $this->verifyEmailHelper->validateEmailConfirmation(
            $this->request->getUri(),
            $user->getPseudo(),
            $user->getMail()
        );
        $user->setIsVerified(true);

        $this->entityManager->flush();
    }

    public function isXmlHttpRequest(Request $request): bool|null
    {
        return $request->query->get('ajax');
    }
}

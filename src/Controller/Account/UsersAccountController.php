<?php

namespace App\Controller\Account;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\TokenGeneratorService;
use App\Service\MailService;
use App\Repository\UsersRepository;
use App\Repository\CompaniesRepository;
use App\Form\UsersType;
use App\Entity\Users;

#[Route('/account/users')]
class UsersAccountController extends AbstractController
{
    private $security;
    private $currentUser;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->currentUser = $this->security->getUser();
    }

    #[Route('/list/{token}', name: 'app_users_account_index', methods: ['GET'])]
    public function index(
        UsersRepository $usersRepository,
        CompaniesRepository $companiesRepository,
        $token
    ): Response
    {
        $owner = $companiesRepository->findOneBy(['token' => $token]);
        return $this->render('account/users/index.html.twig', [
            'users' => $usersRepository->findBy(['fk_company' => $owner]),
        ]);
    }

    #[Route('/new', name: 'app_users_account_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        UsersRepository $usersRepository,
        ResetPasswordHelperInterface $resetPasswordHelper,
        TokenGeneratorService $tokenGeneratorService,
        MailService $mail
    ): Response
    {
        $current_user = $usersRepository->find($this->currentUser->getId());
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_CLIENT']);
            $user->setToken($tokenGeneratorService->generate());
            $user->setFkCompany($current_user->getFkCompany());
            $entityManager->persist($user);
            $entityManager->flush();
            $resetToken = $resetPasswordHelper->generateResetToken($user);
            $datas = [
                'to' => $user->getEmail(),
                'tpl' => 'user_creation',
                'vars' => [
                    'user' => $user,
                    'resetToken' => $resetToken
                    ]
                ];
            $mail->sendMail($datas);

            return $this->redirectToRoute('app_users_account_index', ['token' => $current_user->getFkCompany()->getToken()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/users/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{token?}', name: 'app_users_account_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Users $user, 
        EntityManagerInterface $entityManager,
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $current_user = $usersRepository->find($this->currentUser->getId());
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uow = $entityManager->getUnitOfWork();
            $oldValues = $uow->getOriginalEntityData($user);
            if($user->getPassword() != '') {
                $hashPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashPassword);
            } else {
                $user->setPassword($oldValues['password']);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_users_account_index', ['token' => $current_user->getFkCompany()->getToken()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{token}', name: 'app_users_account_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Users $user, 
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {

            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }
}

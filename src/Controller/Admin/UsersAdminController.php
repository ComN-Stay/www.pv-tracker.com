<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\TokenGeneratorService;
use App\Service\MailService;
use App\Repository\UsersRepository;
use App\Form\UsersType;
use App\Entity\Users;

#[Route('/admin/users')]
class UsersAdminController extends AbstractController
{
    #[Route('/', name: 'app_users_index', methods: ['GET'])]
    public function index(UsersRepository $usersRepository): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $usersRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_users_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        ResetPasswordHelperInterface $resetPasswordHelper,
        TokenGeneratorService $tokenGeneratorService,
        MailService $mail
    ): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_ADMIN_CLIENT']);
            $user->setToken($tokenGeneratorService->generate());
            $user->setAccessDocuments(true);
            $user->setAccessProfile(true);
            $user->setAccessProjects(true);
            $user->setAccessUsers(true);
            $user->setAlertEstimate(true);
            $user->setAlertInvoice(true);
            $user->setAlertRefunds(true);
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

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/users/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_users_show', methods: ['GET'])]
    public function show(
        Users $user
    ): Response
    {
        return $this->render('admin/users/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_users_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Users $user, 
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_users_delete', methods: ['POST'])]
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

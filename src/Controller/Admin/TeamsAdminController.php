<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailService;
use App\Repository\TeamsRepository;
use App\Form\TeamsType;
use App\Entity\Teams;

#[Route('/admin/teams')]
class TeamsAdminController extends AbstractController
{
    #[Route('/', name: 'app_teams_admin_index', methods: ['GET'])]
    public function index(TeamsRepository $teamsRepository): Response
    {
        return $this->render('admin/teams/index.html.twig', [
            'teams' => $teamsRepository->findAll(),
            'sidebar' => 'user'
        ]);
    }

    #[Route('/new', name: 'app_teams_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        ResetPasswordHelperInterface $resetPasswordHelper,
        MailService $mail
    ): Response
    {
        $team = new Teams();
        $form = $this->createForm(TeamsType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $team->setRoles([$form->get('roles')->getData()]);
            $team->setPassword('new');
            $entityManager->persist($team);
            $entityManager->flush();
            $resetToken = $resetPasswordHelper->generateResetToken($team);
            $mail->sendMail([
                'to' => $team->getEmail(),
                'tpl' => 'user_creation',
                'vars' => [
                    'user' => $team,
                    'resetToken' => $resetToken,
                ]
            ]);
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('app_teams_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/teams/new.html.twig', [
            'team' => $team,
            'form' => $form,
            'sidebar' => 'user'
        ]);
    }

    #[Route('/{id}', name: 'app_teams_admin_show', methods: ['GET'])]
    public function show(Teams $team): Response
    {
        return $this->render('admin/teams/show.html.twig', [
            'team' => $team,
            'sidebar' => 'user'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_teams_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Teams $team, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $form = $this->createForm(TeamsType::class, $team, [
            'role' => $team->getRoles()[0]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uow = $entityManager->getUnitOfWork();
            $oldValues = $uow->getOriginalEntityData($team);
            if($team->getPassword() != '') {
                $hashPassword = $passwordHasher->hashPassword($team, $team->getPassword());
                $team->setPassword($hashPassword);
            } else {
                $team->setPassword($oldValues['password']);
            }
            $team->setRoles([$form->get('roles')->getData()]);
            $entityManager->flush();

            return $this->redirectToRoute('app_teams_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/teams/edit.html.twig', [
            'team' => $team,
            'form' => $form,
            'sidebar' => 'user'
        ]);
    }

    #[Route('/{id}', name: 'app_teams_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Teams $team, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_teams_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}

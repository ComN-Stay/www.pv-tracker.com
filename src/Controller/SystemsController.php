<?php

namespace App\Controller;

use App\Entity\Systems;
use App\Form\SystemsType;
use App\Repository\SystemsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/systems')]
class SystemsController extends AbstractController
{
    #[Route('/', name: 'app_systems_index', methods: ['GET'])]
    public function index(SystemsRepository $systemsRepository): Response
    {
        return $this->render('systems/index.html.twig', [
            'systems' => $systemsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_systems_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $system = new Systems();
        $form = $this->createForm(SystemsType::class, $system);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($system);
            $entityManager->flush();

            return $this->redirectToRoute('app_systems_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('systems/new.html.twig', [
            'system' => $system,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_systems_show', methods: ['GET'])]
    public function show(Systems $system): Response
    {
        return $this->render('systems/show.html.twig', [
            'system' => $system,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_systems_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Systems $system, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SystemsType::class, $system);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_systems_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('systems/edit.html.twig', [
            'system' => $system,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_systems_delete', methods: ['POST'])]
    public function delete(Request $request, Systems $system, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$system->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($system);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_systems_index', [], Response::HTTP_SEE_OTHER);
    }
}

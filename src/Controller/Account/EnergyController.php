<?php

namespace App\Controller\Account;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\EnergyPersistanceService;
use App\Repository\EnergyRepository;
use App\Form\EnergyType;
use App\Entity\Energy;
use App\Repository\MonthsRepository;

#[Route('/account/energy')]
class EnergyController extends AbstractController
{
    #[Route('/', name: 'app_energy_index', methods: ['GET'])]
    public function index(
        EnergyRepository $energyRepository,
        MonthsRepository $monthsRepository,
    ): Response
    {
        return $this->render('account/energy/index.html.twig', [
            'energies' => $energyRepository->findAll(),
            'exploitation' => $monthsRepository->findTotalSums()
        ]);
    }

    #[Route('/new', name: 'app_energy_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EnergyPersistanceService $energyPersistanceService
    ): Response
    {
        $energy = new Energy();
        $date = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
        $form = $this->createForm(EnergyType::class, $energy, [
            'date' => $date
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $energyPersistanceService->persist($energy);

            $this->addFlash('success', ['title' => 'Super !', 'text' => 'Nouvelle relève enregistrée !']);

            return $this->redirectToRoute('app_energy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/energy/new.html.twig', [
            'energy' => $energy,
            'form' => $form,
            'date' => $date->format('Y-m-d')
        ]);
    }

    #[Route('/{id}/edit', name: 'app_energy_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Energy $energy, 
        EnergyPersistanceService $energyPersistanceService
    ): Response
    {
        $form = $this->createForm(EnergyType::class, $energy, [
            'date' => $energy->getDate()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $energyPersistanceService->persist($energy);

            $this->addFlash('success', ['title' => 'Tout est OK !', 'text' => 'Relève modifiée !']);

            return $this->redirectToRoute('app_energy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/energy/edit.html.twig', [
            'energy' => $energy,
            'form' => $form,
            'date' => $energy->getDate()
        ]);
    }

    #[Route('/{id}', name: 'app_energy_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Energy $energy, 
        EntityManagerInterface $entityManager,
        EnergyPersistanceService $energyPersistanceService
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$energy->getId(), $request->getPayload()->get('_token'))) {
            $energyPersistanceService->remove($energy);
            $entityManager->remove($energy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_energy_index', [], Response::HTTP_SEE_OTHER);
    }

}

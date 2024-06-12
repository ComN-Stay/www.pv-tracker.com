<?php

namespace App\Controller\Account;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PricesRepository;
use App\Form\PricesType;
use App\Entity\Prices;

#[Route('/prices')]
class PricesController extends AbstractController
{
    
    #[Route('/new', name: 'app_prices_new', methods: ['GET', 'POST'])]
    public function new(
        EntityManagerInterface $entityManager,
        PricesRepository $pricesRepository,
        Request $request,
        Security $security
    ): Response
    {   
        $date = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

        $testConsumption = $pricesRepository->findBy(['fk_user' => $security->getUser(), 'type' => 'consumption']);
        $testResale = $pricesRepository->findBy(['fk_user' => $security->getUser(), 'type' => 'resale']);
        $testSubscription = $pricesRepository->findBy(['fk_user' => $security->getUser(), 'type' => 'subscription']);

        $prices = new Prices();
        $form = $this->createForm(PricesType::class, $prices, [
            'active' => 1,
            'date' => $date
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prices->setFkUser($security->getUser());
            $entityManager->persist($prices);
            $entityManager->flush();

            $testConsumption = $pricesRepository->findBy(['fk_user' => $security->getUser(), 'type' => 'consumption']);
            $testResale = $pricesRepository->findBy(['fk_user' => $security->getUser(), 'type' => 'resale']);
            $testSubscription = $pricesRepository->findBy(['fk_user' => $security->getUser(), 'type' => 'subscription']);

            if(empty($testConsumption) || empty($testResale) || empty($testSubscription)) {
                $this->addFlash('success', ['title' => 'Nouveau tarif enregistré !', 'text' => 'ATTENTION !!! Il vous reste des tarifs à renseigner']);
                return $this->redirectToRoute('app_prices_new', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('success', ['title' => 'Super !', 'text' => 'Nouveau tarif enregistré !']);
                return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('account/prices/new.html.twig', [
            'prices' => $prices,
            'form' => $form,
            'allPrices' => $pricesRepository->findBy(['fk_user' => $security->getUser()]),
            'testConsumption' => $testConsumption,
            'testResale' => $testResale,
            'testSubscription' => $testSubscription,
            'date' => $date->format('Y-m-d')
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prices_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Prices $prices, 
        EntityManagerInterface $entityManager,
        PricesRepository $pricesRepository,
        Security $security
    ): Response
    {
        $date = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

        $form = $this->createForm(PricesType::class, $prices, [
            'active' => $prices->isActive(),
            'date' => $prices->getDateStart()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prices->setFkUser($security->getUser());
            $entityManager->flush();

            $this->addFlash('success', ['title' => 'Tout est OK !', 'text' => 'Tarif modifié !']);

            return $this->redirectToRoute('app_prices_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/prices/edit.html.twig', [
            'prices' => $prices,
            'form' => $form,
            'allPrices' => $pricesRepository->findBy(['fk_user' => $security->getUser()]),
            'date' => $date->format('Y-m-d')
        ]);
    }

    #[Route('/{id}', name: 'app_prices_delete', methods: ['POST'])]
    public function delete(Request $request, Prices $prices, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prices->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($prices);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prices_new', [], Response::HTTP_SEE_OTHER);
    }
}

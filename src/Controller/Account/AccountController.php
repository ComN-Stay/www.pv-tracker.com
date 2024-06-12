<?php

namespace App\Controller\Account;

use App\Repository\EnergyRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\PricesRepository;
use App\Repository\IndexesRepository;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(
        IndexesRepository $indexesRepository,
        PricesRepository $pricesRepository,
        EnergyRepository $energyRepository,
        Security $security
    ): Response
    {
        $date = new \DateTime(date('Y-m-d'));
        $date->modify('-1 day');

        return $this->render('account/index.html.twig', [
            'indexes' => $indexesRepository->findOneBy(['fk_user' => $security->getUser()]),
            'prices' => $pricesRepository->findBy(['fk_user' => $security->getUser(), 'active' => 1]),
            'yesterday' => $energyRepository->findBy(['fk_user' => $security->getUser(), 'date' => $date]),
            'date' => $date,
            'testConsumption' => $pricesRepository->findBy(['fk_user' => $security->getUser(), 'type' => 'consumption']),
            'testResale' => $pricesRepository->findBy(['fk_user' => $security->getUser(), 'type' => 'resale']),
            'testSubscription' => $pricesRepository->findBy(['fk_user' => $security->getUser(), 'type' => 'subscription']),
        ]);
    }
}

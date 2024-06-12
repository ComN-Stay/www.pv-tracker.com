<?php

namespace App\Service;

use App\Entity\Months;
use App\Repository\MonthsRepository;
use App\Repository\PricesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class EnergyPersistanceService
{
    private $entityManager;
    private $pricesRepository;
    private $monthsRepository;
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        PricesRepository $pricesRepository,
        MonthsRepository $monthsRepository,
        Security $security
    )
    {
        $this->entityManager = $entityManager;
        $this->pricesRepository = $pricesRepository;
        $this->monthsRepository = $monthsRepository;
        $this->security = $security;
    }

    public function persist($energy)
    {
        $update = false;

        /* if it's an update we get old values */
        if(NULL !== $energy->getId()) {
            $uow = $this->entityManager->getUnitOfWork();
            $oldValues = $uow->getOriginalEntityData($energy);
            $update = true;
        }

        /* first we find the energy prices (import and export) and subscribtion fees */
        $import_amount = $this->pricesRepository->findImportPriceByDate($energy->getDate());
        $export_amount = $this->pricesRepository->findOneBy(['type' => 'resale']);
        $subscription_amount = $this->pricesRepository->findSubscriptionByDate($energy->getDate());

        /* now we calculate new indexes and prices */
        $self_consumption = $energy->getProduction() - $energy->getExport();
        $global_consumption = $self_consumption + $energy->getImport();
        $import_cost = $import_amount->getAmount() * $energy->getImport();
        $export_cost = $export_amount->getAmount() * $energy->getExport();

        /* then we persist daily indexes ans costs */
        $energy->setImportCost($import_cost);
        $energy->setExportIncome($export_cost);
        $energy->setSavings($import_amount->getAmount() * $self_consumption);
        $energy->setBalance($export_cost - $import_cost);
        $energy->setSelf($self_consumption);
        $energy->setConsumption($global_consumption);
        $energy->setFkUser($this->security->getUser());
        $this->entityManager->persist($energy);
        $this->entityManager->flush();

        /* we get month and year from date */
        $splitDate = explode('-', $energy->getDate()->format('Y-m-d'));
        $currentMonth = $splitDate[1];
        $currentYear = $splitDate[0];

        /* we do the same with mounthly entity */
        $month = $this->monthsRepository->findOneBy(['month' => $currentMonth, 'year' => $currentYear]);
        if(NULL === $month) {
            $newMonth = new Months;
            $newMonth->setMonth($currentMonth);
            $newMonth->setYear($currentYear);
            $newMonth->setFkUser($this->security->getUser());
            $newMonth->setSubscriptionFees($subscription_amount->getAmount());
            $this->entityManager->persist($newMonth);
            $this->entityManager->flush();
            $month = $this->monthsRepository->findOneBy(['month' => $currentMonth, 'year' => $currentYear]);
        }

        /* if it's a new energy we do the sums */
        if($update == false) {
            $month->setProduction((float)$energy->getProduction() + (float)$month->getProduction());
            $month->setImport((float)$energy->getImport() + (float)$month->getImport());
            $month->setExport((float)$energy->getExport() + (float)$month->getExport());
            $month->setImportCost((float)$import_cost + (float)$month->getImportCost());
            $month->setExportIncome((float)$export_cost + (float)$month->getExportIncome());
            $month->setSavings(((float)$import_amount->getAmount() * (float)$self_consumption) + (float)$month->getSavings());
            $month->setBalance(((float)$export_cost - (float)$import_cost) + (float)$month->getBalance());
            $month->setSelf((float)$self_consumption + (float)$month->getSelf());
            $month->setConsumption((float)$global_consumption + (float)$month->getConsumption());
        } else {
            $month->setProduction((float)$energy->getProduction() + (float)$month->getProduction() - $oldValues['production']);
            $month->setImport((float)$energy->getImport() + (float)$month->getImport() - $oldValues['import']);
            $month->setExport((float)$energy->getExport() + (float)$month->getExport() - $oldValues['export']);
            $month->setImportCost((float)$import_cost + (float)$month->getImportCost() - $oldValues['import_cost']);
            $month->setExportIncome((float)$export_cost + (float)$month->getExportIncome() - $oldValues['export_income']);
            $month->setSavings((((float)$import_amount->getAmount() * (float)$self_consumption) + (float)$month->getSavings()) - $oldValues['savings']);
            $month->setBalance(((float)$export_cost - (float)$import_cost) + (float)$month->getBalance() - $oldValues['balance']);
            $month->setSelf((float)$self_consumption + (float)$month->getSelf() - $oldValues['self']);
            $month->setConsumption((float)$global_consumption + (float)$month->getConsumption() - $oldValues['consumption']);
        }
        $month->setMonth($month->getMonth());
        $month->setYear($month->getYear());
        $month->setFkUser($this->security->getUser());
        $this->entityManager->persist($month);
        $this->entityManager->flush();
    }

    public function remove($energy)
    {
        $splitDate = explode('-', $energy->getDate()->format('Y-m-d'));
        $currentMonth = $splitDate[1];
        $currentYear = $splitDate[0];

        /* we do the same with mounthly entity */
        $month = $this->monthsRepository->findOneBy(['month' => $currentMonth, 'year' => $currentYear]);

        $month->setProduction((float)$month->getProduction() - (float)$energy->getProduction());
        $month->setImport((float)$month->getImport() - (float)$energy->getImport());
        $month->setExport((float)$month->getExport() - (float)$energy->getExport());
        $month->setImportCost((float)$month->getImportCost() - (float)$energy->getImportCost());
        $month->setExportIncome((float)$month->getExportIncome() - (float)$energy->getExportIncome());
        $month->setSavings((float)$month->getSavings() - (float)$energy->getSavings());
        $month->setBalance((float)$month->getBalance() - (float)$energy->getBalance());
        $month->setSelf((float)$month->getSelf() - (float)$energy->getSelf());
        $month->setConsumption((float)$month->getConsumption() - (float)$energy->getConsumption());
        $month->setMonth($month->getMonth());
        $month->setYear($month->getYear());
        $month->setFkUser($this->security->getUser());
        $this->entityManager->persist($month);
        $this->entityManager->flush();
    }

}
<?php

namespace App\Repository;

use App\Entity\Months;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Months>
 */
class MonthsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Months::class);
    }

    public function findTotalSums()
    {
        return $this->createQueryBuilder('m')
            ->select('SUM(m.production) AS production, SUM(m.import) AS importkwh, SUM(m.export) AS export, 
                SUM(m.self) AS self, SUM(m.import_cost) AS importCost, SUM(m.export_income) AS exportIncome, 
                SUM(m.savings) AS savings, SUM(m.balance) AS balance, SUM(m.consumption) AS consumption')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Months[] Returns an array of Months objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Months
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

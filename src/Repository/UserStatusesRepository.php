<?php

namespace App\Repository;

use App\Entity\UserStatuses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserStatuses>
 *
 * @method UserStatuses|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserStatuses|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserStatuses[]    findAll()
 * @method UserStatuses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserStatusesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserStatuses::class);
    }

//    /**
//     * @return UserStatuses[] Returns an array of UserStatuses objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserStatuses
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

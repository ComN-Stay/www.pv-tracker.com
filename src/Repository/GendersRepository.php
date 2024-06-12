<?php

namespace App\Repository;

use App\Entity\Genders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Genders>
 *
 * @method Genders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genders[]    findAll()
 * @method Genders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GendersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genders::class);
    }

//    /**
//     * @return Genders[] Returns an array of Genders objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Genders
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\BlocEcheancier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlocEcheancier>
 *
 * @method BlocEcheancier|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlocEcheancier|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlocEcheancier[]    findAll()
 * @method BlocEcheancier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlocEcheancierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlocEcheancier::class);
    }

//    /**
//     * @return BlocEcheancier[] Returns an array of BlocEcheancier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BlocEcheancier
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

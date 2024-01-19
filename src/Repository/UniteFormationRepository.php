<?php

namespace App\Repository;

use App\Entity\UniteFormation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UniteFormation>
 *
 * @method UniteFormation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UniteFormation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UniteFormation[]    findAll()
 * @method UniteFormation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UniteFormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UniteFormation::class);
    }

//    /**
//     * @return UniteFormation[] Returns an array of UniteFormation objects
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

//    public function findOneBySomeField($value): ?UniteFormation
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\LigneDeliberation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneDeliberation>
 *
 * @method LigneDeliberation|null find($id, $lockMode = null, $lockVersion = null)
 * @method LigneDeliberation|null findOneBy(array $criteria, array $orderBy = null)
 * @method LigneDeliberation[]    findAll()
 * @method LigneDeliberation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneDeliberationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneDeliberation::class);
    }

//    /**
//     * @return LigneDeliberation[] Returns an array of LigneDeliberation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LigneDeliberation
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

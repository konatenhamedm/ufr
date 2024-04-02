<?php

namespace App\Repository;

use App\Entity\ValeurNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ValeurNote>
 *
 * @method ValeurNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValeurNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValeurNote[]    findAll()
 * @method ValeurNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValeurNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValeurNote::class);
    }

//    /**
//     * @return ValeurNote[] Returns an array of ValeurNote objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ValeurNote
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

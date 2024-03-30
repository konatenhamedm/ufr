<?php

namespace App\Repository;

use App\Entity\TypeControle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeControle>
 *
 * @method TypeControle|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeControle|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeControle[]    findAll()
 * @method TypeControle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeControleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeControle::class);
    }

//    /**
//     * @return TypeControle[] Returns an array of TypeControle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypeControle
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

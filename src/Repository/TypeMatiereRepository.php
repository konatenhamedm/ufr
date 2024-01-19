<?php

namespace App\Repository;

use App\Entity\TypeMatiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeMatiere>
 *
 * @method TypeMatiere|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeMatiere|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeMatiere[]    findAll()
 * @method TypeMatiere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeMatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeMatiere::class);
    }

//    /**
//     * @return TypeMatiere[] Returns an array of TypeMatiere objects
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

//    public function findOneBySomeField($value): ?TypeMatiere
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\MoyenneMatiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MoyenneMatiere>
 *
 * @method MoyenneMatiere|null find($id, $lockMode = null, $lockVersion = null)
 * @method MoyenneMatiere|null findOneBy(array $criteria, array $orderBy = null)
 * @method MoyenneMatiere[]    findAll()
 * @method MoyenneMatiere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoyenneMatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MoyenneMatiere::class);
    }

//    /**
//     * @return MoyenneMatiere[] Returns an array of MoyenneMatiere objects
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

//    public function findOneBySomeField($value): ?MoyenneMatiere
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

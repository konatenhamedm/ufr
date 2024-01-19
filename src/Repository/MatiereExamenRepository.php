<?php

namespace App\Repository;

use App\Entity\MatiereExamen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MatiereExamen>
 *
 * @method MatiereExamen|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatiereExamen|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatiereExamen[]    findAll()
 * @method MatiereExamen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatiereExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatiereExamen::class);
    }

//    /**
//     * @return MatiereExamen[] Returns an array of MatiereExamen objects
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

//    public function findOneBySomeField($value): ?MatiereExamen
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

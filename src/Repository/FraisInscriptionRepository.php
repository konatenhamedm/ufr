<?php

namespace App\Repository;

use App\Entity\FraisInscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FraisInscription>
 *
 * @method FraisInscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method FraisInscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method FraisInscription[]    findAll()
 * @method FraisInscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FraisInscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FraisInscription::class);
    }

//    /**
//     * @return FraisInscription[] Returns an array of FraisInscription objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FraisInscription
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

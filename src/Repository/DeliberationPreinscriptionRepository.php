<?php

namespace App\Repository;

use App\Entity\DeliberationPreinscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeliberationPreinscription>
 *
 * @method DeliberationPreinscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliberationPreinscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliberationPreinscription[]    findAll()
 * @method DeliberationPreinscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliberationPreinscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliberationPreinscription::class);
    }

//    /**
//     * @return DeliberationPreinscription[] Returns an array of DeliberationPreinscription objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DeliberationPreinscription
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

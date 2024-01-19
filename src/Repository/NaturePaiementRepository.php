<?php

namespace App\Repository;

use App\Entity\NaturePaiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NaturePaiement>
 *
 * @method NaturePaiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method NaturePaiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method NaturePaiement[]    findAll()
 * @method NaturePaiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NaturePaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NaturePaiement::class);
    }

//    /**
//     * @return NaturePaiement[] Returns an array of NaturePaiement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NaturePaiement
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

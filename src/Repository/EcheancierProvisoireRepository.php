<?php

namespace App\Repository;

use App\Entity\EcheancierProvisoire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EcheancierProvisoire>
 *
 * @method EcheancierProvisoire|null find($id, $lockMode = null, $lockVersion = null)
 * @method EcheancierProvisoire|null findOneBy(array $criteria, array $orderBy = null)
 * @method EcheancierProvisoire[]    findAll()
 * @method EcheancierProvisoire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcheancierProvisoireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EcheancierProvisoire::class);
    }

//    /**
//     * @return EcheancierProvisoire[] Returns an array of EcheancierProvisoire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EcheancierProvisoire
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

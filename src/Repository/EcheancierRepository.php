<?php

namespace App\Repository;

use App\Entity\Echeancier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Echeancier>
 *
 * @method Echeancier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Echeancier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Echeancier[]    findAll()
 * @method Echeancier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcheancierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Echeancier::class);
    }

    public function findAllEcheance($value): array
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.inscription', 'l')
            ->andWhere('l.id = :id')
            ->andWhere('f.etat = :etat')
            ->setParameter('etat', 'pas_payer')
            ->setParameter('id', $value)
            ->orderBy('f.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Echeancier[] Returns an array of Echeancier objects
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

    //    public function findOneBySomeField($value): ?Echeancier
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

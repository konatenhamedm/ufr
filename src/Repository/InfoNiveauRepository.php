<?php

namespace App\Repository;

use App\Entity\InfoNiveau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoNiveau>
 *
 * @method InfoNiveau|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoNiveau|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoNiveau[]    findAll()
 * @method InfoNiveau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoNiveauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoNiveau::class);
    }

//    /**
//     * @return InfoNiveau[] Returns an array of InfoNiveau objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InfoNiveau
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

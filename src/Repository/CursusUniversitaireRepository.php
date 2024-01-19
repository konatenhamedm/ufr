<?php

namespace App\Repository;

use App\Entity\CursusUniversitaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CursusUniversitaire>
 *
 * @method CursusUniversitaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method CursusUniversitaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method CursusUniversitaire[]    findAll()
 * @method CursusUniversitaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CursusUniversitaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CursusUniversitaire::class);
    }

//    /**
//     * @return CursusUniversitaire[] Returns an array of CursusUniversitaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CursusUniversitaire
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

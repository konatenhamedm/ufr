<?php

namespace App\Repository;

use App\Entity\InfoEtudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoEtudiant>
 *
 * @method InfoEtudiant|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoEtudiant|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoEtudiant[]    findAll()
 * @method InfoEtudiant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoEtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoEtudiant::class);
    }

//    /**
//     * @return InfoEtudiant[] Returns an array of InfoEtudiant objects
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

//    public function findOneBySomeField($value): ?InfoEtudiant
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

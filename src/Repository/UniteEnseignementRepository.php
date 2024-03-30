<?php

namespace App\Repository;

use App\Entity\UniteEnseignement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UniteEnseignement>
 *
 * @method UniteEnseignement|null find($id, $lockMode = null, $lockVersion = null)
 * @method UniteEnseignement|null findOneBy(array $criteria, array $orderBy = null)
 * @method UniteEnseignement[]    findAll()
 * @method UniteEnseignement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UniteEnseignementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UniteEnseignement::class);
    }

//    /**
//     * @return UniteEnseignement[] Returns an array of UniteEnseignement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UniteEnseignement
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

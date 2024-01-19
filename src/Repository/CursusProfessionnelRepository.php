<?php

namespace App\Repository;

use App\Entity\CursusProfessionnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CursusProfessionnel>
 *
 * @method CursusProfessionnel|null find($id, $lockMode = null, $lockVersion = null)
 * @method CursusProfessionnel|null findOneBy(array $criteria, array $orderBy = null)
 * @method CursusProfessionnel[]    findAll()
 * @method CursusProfessionnel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CursusProfessionnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CursusProfessionnel::class);
    }

//    /**
//     * @return CursusProfessionnel[] Returns an array of CursusProfessionnel objects
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

//    public function findOneBySomeField($value): ?CursusProfessionnel
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

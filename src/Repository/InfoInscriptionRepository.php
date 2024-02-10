<?php

namespace App\Repository;

use App\Entity\InfoInscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoInscription>
 *
 * @method InfoInscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoInscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoInscription[]    findAll()
 * @method InfoInscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoInscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoInscription::class);
    }

//    /**
//     * @return InfoInscription[] Returns an array of InfoInscription objects
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

//    public function findOneBySomeField($value): ?InfoInscription
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

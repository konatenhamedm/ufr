<?php

namespace App\Repository;

use App\Entity\InfoPreinscription;
use App\Entity\Preinscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoPreinscription>
 *
 * @method InfoPreinscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoPreinscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoPreinscription[]    findAll()
 * @method InfoPreinscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoPreinscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoPreinscription::class);
    }
    public function add(InfoPreinscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return InfoPreinscription[] Returns an array of InfoPreinscription objects
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

//    public function findOneBySomeField($value): ?InfoPreinscription
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

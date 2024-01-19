<?php

namespace App\Repository;

use App\Entity\Versement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Versement>
 *
 * @method Versement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Versement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Versement[]    findAll()
 * @method Versement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Versement::class);
    }


    public function lastNumero($annee)
    {
        return $this->createQueryBuilder('a')
        ->select("a.reference")
        ->orderBy('CAST(SUBSTRING(a.reference, -4) AS UNSIGNED)', 'DESC')
        ->andWhere('YEAR(a.dateVersement) = :annee')
        
        
        ->setParameter('annee', $annee)
        
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }


    public function nextNumero($annee)
    {
        $data = $this->lastNumero($annee);
        if ($data && $data['reference']) {
            $reference = $data['reference'];
           
            if (strpos($reference, '-') !== false) {
                [, $numero] = explode('-', $reference);
                $numero = ltrim($numero, '0');
            } else {
                $numero = 0;
            }
        } else {
            $numero = 0;
        }

        $code = "UP";
        $chrono = str_pad($numero + 1 , 4, '0', STR_PAD_LEFT);
        $annee = substr($annee, -2);

        return "{$code}{$annee}-{$chrono}";
    }

//    /**
//     * @return Versement[] Returns an array of Versement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Versement
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

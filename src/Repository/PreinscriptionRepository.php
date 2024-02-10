<?php

namespace App\Repository;

use App\Entity\Deliberation;
use App\Entity\DeliberationPreinscription;
use App\Entity\Examen;
use App\Entity\NiveauEtudiant;
use App\Entity\Preinscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Preinscription>
 *
 * @method Preinscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Preinscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Preinscription[]    findAll()
 * @method Preinscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreinscriptionRepository extends ServiceEntityRepository
{

    private $user;
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Preinscription::class);
        $this->user = $security->getUser();
    }
    public function add(Preinscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLastRecord()
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.utilisateur', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $this->user->getId())
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function withoutDeliberation(Examen $examen)
    {
        $qb = $this->createQueryBuilder('p');
        $qbExists = $this->getEntityManager()->createQueryBuilder('a');
        $stmtExists = $qbExists->select('d')->from(Deliberation::class, 'd')
            ->join(DeliberationPreinscription::class, 'dl', 'WITH', 'dl.deliberation = d.id')
            ->andWhere('dl.preinscription = p.id')
            ->andWhere('d.examen = :examen');

        $qb->select('p')
            ->andWhere($qb->expr()->not($qb->expr()->exists($stmtExists->getDQL())))
            ->andWhere('p.niveau = :niveau')
            ->andWhere('p.etat = :etat')
            ->andWhere('p.etatDeliberation = :deliberation')
            ->setParameter('niveau', $examen->getNiveau())
            ->setParameter('examen', $examen)
            ->setParameter('etat', 'valide')
            ->setParameter('deliberation', 'pas_deliberer');

        return $qb;
    }


    //    /**
    //     * @return Preinscription[] Returns an array of Preinscription objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Preinscription
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

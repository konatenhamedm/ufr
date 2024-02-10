<?php

namespace App\Repository;

use App\Entity\Filiere;
use App\Entity\Niveau;
use App\Entity\NiveauEtudiant;
use App\Entity\Preinscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Niveau>
 *
 * @method Niveau|null find($id, $lockMode = null, $lockVersion = null)
 * @method Niveau|null findOneBy(array $criteria, array $orderBy = null)
 * @method Niveau[]    findAll()
 * @method Niveau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NiveauRepository extends ServiceEntityRepository
{
    private $em;
    private $user;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, Security $security)
    {
        parent::__construct($registry, Niveau::class);
        $this->em = $entityManager;
        $this->user = $security->getUser();
    }

    public function add(NiveauEtudiant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public  function findNiveauDisponible()
    {
        $qb = $this->em->createQueryBuilder();

        $linked = $qb->select('n.id', 'rl.id as rlid')
            ->from(Preinscription::class, 'rl')
            ->innerJoin('rl.niveau', 'n')
            ->innerJoin('rl.utilisateur', 'u')
            ->andWhere('u.id =:id')
            ->setParameter('id', $this->user->getId())
            ->getQuery()
            ->getResult();


        $qb2 = $this->createQueryBuilder('e');
        $qb2->select('e')
            ->where('e.id not IN (:id)')
            ->setParameter('id', $linked);

        return $qb2;
    }



    //    /**
    //     * @return Niveau[] Returns an array of Niveau objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Niveau
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

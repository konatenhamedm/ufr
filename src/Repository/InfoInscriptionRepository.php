<?php

namespace App\Repository;

use App\Entity\Etudiant;
use App\Entity\Filiere;
use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Entity\Niveau;
use App\Entity\Personne;
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
    use TableInfoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoInscription::class);
    }

    public function getData($etat)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.modePaiement', 'u')
            ->andWhere('u.code = :code')
            ->andWhere('p.etat = :etat')
            ->setParameter('etat', $etat)
            ->setParameter('code', 'CHQ')
            ->getQuery()
            ->getResult();
    }
    public function getDataPaiementEffectue($id)
    {
        return $this->createQueryBuilder('e')
            ->join('e.modePaiement', 'mode')
            ->join('e.inscription', 'i')
            ->join('e.caissiere', 'c')
            ->andWhere('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function getListeRecouvrement()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableInfo = $this->getTableName(InfoInscription::class, $em);
        $tableInscription = $this->getTableName(Inscription::class, $em);
        $tableUser = $this->getTableName(Etudiant::class, $em);
        $tablePersonne = $this->getTableName(Personne::class, $em);
        $tableFiliere = $this->getTableName(Filiere::class, $em);
        $tableNiveau = $this->getTableName(Niveau::class, $em);

        //dd($dateDebut,$dateFin);


        $sql = <<<SQL
SELECT e.id AS _etudiant_id,p.nom,p.prenom,i.montant,n.id as niveau
FROM {$tableInfo} d
Left JOIN {$tableInscription} i ON d.inscription_id = i.id
Inner JOIN {$tableUser} e ON e.id = i.etudiant_id
Inner JOIN {$tableNiveau} n ON n.id = i.niveau_id
Inner JOIN {$tablePersonne} p ON p.id = e.id
Inner JOIN {$tableFiliere} f ON f.id = n.filiere_id
WHERE   d.etat  in ('valide')
GROUP BY n.id,_etudiant_id,p.nom,p.prenom,i.montant

SQL;


        //$params[''] = $niveau;
        // $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql);
        return $stmt->fetchAllAssociative();
    }
    public function getListeVersement()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableInfo = $this->getTableName(InfoInscription::class, $em);
        $tableInscription = $this->getTableName(Inscription::class, $em);
        $tableUser = $this->getTableName(Etudiant::class, $em);
        $tablePersonne = $this->getTableName(Personne::class, $em);
        $tableFiliere = $this->getTableName(Filiere::class, $em);
        $tableNiveau = $this->getTableName(Niveau::class, $em);

        //dd($dateDebut,$dateFin);


        $sql = <<<SQL
SELECT e.id AS _etudiant_id,SUM(d.montant) as somme,n.id as niveau,YEAR(d.date_paiement) as year
FROM {$tableInfo} d
Left JOIN {$tableInscription} i ON d.inscription_id = i.id
Inner JOIN {$tableUser} e ON e.id = i.etudiant_id
Inner JOIN {$tableNiveau} n ON n.id = i.niveau_id
Inner JOIN {$tablePersonne} p ON p.id = e.id
Inner JOIN {$tableFiliere} f ON f.id = n.filiere_id
WHERE   d.etat  in ('valide')
GROUP BY n.id,_etudiant_id,year

SQL;


        //$params[''] = $niveau;
        // $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql);
        return $stmt->fetchAllAssociative();
    }
    public function rangeDate()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableInfo = $this->getTableName(InfoInscription::class, $em);
        $tableInscription = $this->getTableName(Inscription::class, $em);
        $tableUser = $this->getTableName(Etudiant::class, $em);
        $tablePersonne = $this->getTableName(Personne::class, $em);
        $tableFiliere = $this->getTableName(Filiere::class, $em);
        $tableNiveau = $this->getTableName(Niveau::class, $em);

        //dd($dateDebut,$dateFin);


        $sql = <<<SQL
SELECT  MIN(YEAR(date_paiement)) AS min_year, MAX(YEAR(date_paiement)) AS max_year
FROM {$tableInfo} d
WHERE   d.etat  in ('valide')

SQL;


        //$params[''] = $niveau;
        // $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql);
        return $stmt->fetchAssociative();
    }
    public function rangeSommeParAnnneNiveauEtudiant($etudiant, $year, $niveau)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableInfo = $this->getTableName(InfoInscription::class, $em);
        $tableInscription = $this->getTableName(Inscription::class, $em);
        $tableUser = $this->getTableName(Etudiant::class, $em);
        $tablePersonne = $this->getTableName(Personne::class, $em);
        $tableFiliere = $this->getTableName(Filiere::class, $em);
        $tableNiveau = $this->getTableName(Niveau::class, $em);

        //dd($dateDebut,$dateFin);


        $sql = <<<SQL
SELECT SUM(d.montant) as somme
FROM {$tableInfo} d
Left JOIN {$tableInscription} i ON d.inscription_id = i.id
Inner JOIN {$tableUser} e ON e.id = i.etudiant_id
Inner JOIN {$tableNiveau} n ON n.id = i.niveau_id
Inner JOIN {$tablePersonne} p ON p.id = e.id
Inner JOIN {$tableFiliere} f ON f.id = n.filiere_id
WHERE   d.etat  in ('valide') and n.id =:niveau and e.id =:etudiant and YEAR(d.date_paiement) =:year
SQL;


        $params['niveau'] = $niveau;
        $params['etudiant'] = $etudiant;
        $params['year'] = $year;


        $stmt = $connection->executeQuery($sql, $params);
        return $stmt->fetchAssociative();
    }
    public function sommeTotal($etudiant, $niveau)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableInfo = $this->getTableName(InfoInscription::class, $em);
        $tableInscription = $this->getTableName(Inscription::class, $em);
        $tableUser = $this->getTableName(Etudiant::class, $em);
        $tablePersonne = $this->getTableName(Personne::class, $em);
        $tableFiliere = $this->getTableName(Filiere::class, $em);
        $tableNiveau = $this->getTableName(Niveau::class, $em);

        //dd($dateDebut,$dateFin);


        $sql = <<<SQL
SELECT SUM(d.montant) as somme
FROM {$tableInfo} d
Left JOIN {$tableInscription} i ON d.inscription_id = i.id
Inner JOIN {$tableUser} e ON e.id = i.etudiant_id
Inner JOIN {$tableNiveau} n ON n.id = i.niveau_id
Inner JOIN {$tablePersonne} p ON p.id = e.id
Inner JOIN {$tableFiliere} f ON f.id = n.filiere_id
WHERE   d.etat  in ('valide') and n.id =:niveau and e.id =:etudiant 


SQL;


        $params['niveau'] = $niveau;
        $params['etudiant'] = $etudiant;


        $stmt = $connection->executeQuery($sql, $params);
        return $stmt->fetchAssociative();
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

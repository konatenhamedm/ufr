<?php


namespace App\Service;


use App\Entity\ArticleMagasin;
use App\Entity\Document;
use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Entity\LigneDocument;
use App\Entity\Mouvement;
use App\Entity\Sens;
use App\Entity\Sortie;
use App\Repository\ArticleMagasinRepository;
use App\Repository\DocumentRepository;
use App\Repository\EcheancierRepository;
use App\Repository\InfoInscriptionRepository;
use App\Repository\LigneDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class Service
{
    private $em;
    private $repository;
    private $infoRepository;
    private $echeancierRepository;
    private $ligneDocument;
    protected $articleMagasinRepository;

    private $security;


    public function __construct(EntityManagerInterface $em, DocumentRepository $documentRepository, InfoInscriptionRepository $infoInscriptionRepository, EcheancierRepository $echeancierRepository, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
        $this->repository = $documentRepository;
        $this->infoRepository = $infoInscriptionRepository;
        $this->echeancierRepository = $echeancierRepository;

        //$this->verifieIfFile2(15,2);
    }


    public  function  getUser()
    {
        return $this->security->getUser();
    }
    public function verifieIfFile($id, $employe)
    {
        $repo = $this->repository->getNombreLigne($id, $employe);
        // dd($repo);
        return $repo;
    }
    public function verifieIfFile2($id, $employe)
    {
        $repo = $this->ligneDocument->getLastFile($id, $employe);
        //dd($repo);
        return $repo;
    }

    /*     public function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Mouvement::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'MVT' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));

    } */

    /* public function miseAjourArticleMagasin($magasin,$article,$quantiteRecue, $sens = null,$magasinDestinataire = null)
    {

        if ($sens instanceof Sens) {
            $sens = $sens->getSens();
        }
        if ($magasinDestinataire != null) {

            $verificationMagasin = $this->articleMagasinRepository->findOneBy(array('article'=>$article,'magasin'=>$magasin));
            $verificationMagasinDestinataire = $this->articleMagasinRepository->findOneBy(array('article'=>$article,'magasin'=>$magasinDestinataire));
            if($verificationMagasin){
                $quantiteMagasin    = $verificationMagasin->getQuantite() + $quantiteRecue * (-1);

                if($verificationMagasinDestinataire){
                    $quantiteMagasinDestinataire    = $verificationMagasinDestinataire->getQuantite() + $quantiteRecue;

                    $verificationMagasin->setQuantite($quantiteMagasin);
                    $verificationMagasinDestinataire->setQuantite($quantiteMagasinDestinataire);
                    $this->em->persist($verificationMagasin);
                    $this->em->persist($verificationMagasinDestinataire);
                    $this->em->flush();
                }else{

                    $verificationMagasin->setQuantite($quantiteMagasin);
                    $newArticleMagasin = new ArticleMagasin();
                    $newArticleMagasin->setArticle($article);
                    $newArticleMagasin->setMagasin($magasinDestinataire);
                    $newArticleMagasin->setQuantite($quantiteRecue);
                    $newArticleMagasin->setSeuil(10);
                    $this->em->persist($verificationMagasin);
                    $this->em->persist($newArticleMagasin);
                    $this->em->flush();

                }




            }


        }else{
            $verification = $this->articleMagasinRepository->findOneBy(array('article'=>$article,'magasin'=>$magasin));
            if($verification){

                $quantieFinale   = $verification->getQuantite() + $sens * $quantiteRecue;

                $verification->setQuantite($quantieFinale);
                $this->em->persist($verification);
                $this->em->flush();
            }else{
                $newArticleMagasin = new ArticleMagasin();
                $newArticleMagasin->setArticle($article);
                $newArticleMagasin->setMagasin($magasin);

                $quantite   = $newArticleMagasin->getQuantite() + $sens * $quantiteRecue;

                $newArticleMagasin->setQuantite($quantite);
                $newArticleMagasin->setSeuil(10);
                $this->em->persist($newArticleMagasin);
                $this->em->flush();
            }
        }


    } */

    public function paiementInscriptionNew(Inscription $inscription, $data = [])
    {


        $paiement = new InfoInscription();
        $paiement->setUtilisateur($this->getUser());
        $paiement->setCode($inscription->getCode());
        $paiement->setDateValidation(new \DateTime());
        $paiement->setInscription($inscription);
        $paiement->setDatePaiement($data['date']);
        $paiement->setCaissiere($this->getUser());
        $paiement->setModePaiement($data['modePaiement']);
        $paiement->setMontant($data['montant']);
        // $paiement->setEchenacier($echeancier);
        if ($data['modePaiement']->getCode() == 'CHQ') {
            $paiement->setNumeroCheque($data['numeroCheque']);
            $paiement->setBanque($data['banque']);
            $paiement->setTireur($data['tireur']);
            $paiement->setContact($data['contact']);
            $paiement->setDateCheque($data['dateCheque']);
        }
        if ($data['modePaiement']->isConfirmation()) {
            $paiement->setEtat('attente_confirmation');
        } else {
            $paiement->setEtat('payer');
        }

        $this->em->persist($paiement);
        $this->em->flush();
        //dd((int)$this->infoRepository->getMontantInfoInscription($inscription));

        $sommeMontant = (int)$this->infoRepository->getMontantInfoInscription($inscription);

        $listeEcheanciers = $this->echeancierRepository->findAllEcheance($inscription->getId());


        foreach ($listeEcheanciers as $key => $echeancier) {

            if ($sommeMontant == 0) {
                break;    /* Vous pourriez aussi utiliser 'break 1;' ici. */
            }

            $totalPayer = (int)$echeancier->getTotaPayer();

            if ($sommeMontant >= $echeancier->getMontant()) {
                $echeancier->setTotaPayer((int)$echeancier->getMontant());
                $echeancier->setEtat('payer');
                $sommeMontant = $sommeMontant - (int)$echeancier->getMontant();
            } else {

                $echeancier->setTotaPayer($sommeMontant);
                $echeancier->setEtat('pas_payer');
                $sommeMontant = 0;
            }


            $this->em->persist($echeancier);
            $this->em->flush();
        }

        $inscription->setTotalPaye($this->infoRepository->getMontantInfoInscription($inscription));

        if ($inscription->getMontant() == $this->infoRepository->getMontantInfoInscription($inscription)) {

            $inscription->setEtat('solde');
        }
        $this->em->persist($inscription);
        $this->em->flush();
    }
    public function paiementInscriptionEdit(Inscription $inscription, InfoInscription $infoInscription, $data = [])
    {
        $paiement = new InfoInscription();
        $paiement->setUtilisateur($this->getUser());
        $paiement->setCode($inscription->getCode());
        $paiement->setDateValidation(new \DateTime());
        $paiement->setInscription($inscription);
        $paiement->setDatePaiement($data['date']);
        $paiement->setCaissiere($this->getUser());
        $paiement->setModePaiement($data['modePaiement']);
        $paiement->setMontant($data['montant']);
        // $paiement->setEchenacier($echeancier);
        if ($data['modePaiement']->getCode() == 'CHQ') {
            $paiement->setNumeroCheque($data['numeroCheque']);
            $paiement->setBanque($data['banque']);
            $paiement->setTireur($data['tireur']);
            $paiement->setContact($data['contact']);
            $paiement->setDateCheque($data['dateCheque']);
        }

        if ($data['modePaiement']->isConfirmation()) {

            $paiement->setEtat('attente_confirmation');
        } else {

            $paiement->setEtat('payer');
        }

        $this->em->persist($paiement);
        $this->em->flush();
    }
}

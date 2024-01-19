<?php


namespace App\Service;


use App\Entity\ArticleMagasin;
use App\Entity\Document;
use App\Entity\LigneDocument;
use App\Entity\Mouvement;
use App\Entity\Sens;
use App\Entity\Sortie;
use App\Repository\ArticleMagasinRepository;
use App\Repository\DocumentRepository;
use App\Repository\LigneDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class Service
{
    private $em;
    private $repository;
    private $ligneDocument;
    protected $articleMagasinRepository;

    private $security;


    public function __construct(EntityManagerInterface $em,DocumentRepository $documentRepository, LigneDocumentRepository $ligneDocument,ArticleMagasinRepository $articleMagasinRepository,Security $security)
    {
        $this->em = $em;
        $this->security = $security;
        $this->repository = $documentRepository;
        $this->ligneDocument = $ligneDocument;
        $this->articleMagasinRepository = $articleMagasinRepository;
        //$this->verifieIfFile2(15,2);
    }


    public  function  getUser(){
        return $this->security->getUser();
    }
    public function verifieIfFile($id,$employe)
    {
        $repo = $this->repository->getNombreLigne($id,$employe);
      // dd($repo);
        return $repo;
    }
    public function verifieIfFile2($id,$employe)
    {
        $repo = $this->ligneDocument->getLastFile($id,$employe);
        //dd($repo);
        return $repo;
    }

    public function numero()
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

    }

    public function miseAjourArticleMagasin($magasin,$article,$quantiteRecue, $sens = null,$magasinDestinataire = null)
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


    }

}
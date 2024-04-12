<?php


namespace App\Service;


use App\Entity\ArticleMagasin;
use App\Entity\Document;
use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Entity\LigneDocument;
use App\Entity\Mouvement;
use App\Entity\MoyenneMatiere;
use App\Entity\Sens;
use App\Entity\Sortie;
use App\Repository\ArticleMagasinRepository;
use App\Repository\ClasseRepository;
use App\Repository\CoursRepository;
use App\Repository\DocumentRepository;
use App\Repository\EcheancierRepository;
use App\Repository\InfoInscriptionRepository;
use App\Repository\LigneDocumentRepository;
use App\Repository\MatiereRepository;
use App\Repository\MatiereUeRepository;
use App\Repository\MoyenneMatiereRepository;
use App\Repository\NoteRepository;
use App\Repository\SemestreRepository;
use App\Repository\SessionRepository;
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
    protected $moyenneMatiereRepository;
    protected $matiereUeRepository;
    protected $matiereRepository;
    protected $classeRepository;
    protected $semestreRepository;
    protected $sessionRepository;
    protected $noteRepository;
    protected $coursRepository;
    private $security;


    public function __construct(
        EntityManagerInterface $em,
        DocumentRepository $documentRepository,
        InfoInscriptionRepository $infoInscriptionRepository,
        EcheancierRepository $echeancierRepository,
        Security $security,
        MoyenneMatiereRepository $moyenneMatiereRepository,
        MatiereUeRepository $matiereUeRepository,
        MatiereRepository $matiereRepository,
        ClasseRepository $classeRepository,
        SemestreRepository $semestreRepository,
        SessionRepository $sessionRepository,
        NoteRepository $noteRepository,
        CoursRepository $coursRepository,
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->repository = $documentRepository;
        $this->infoRepository = $infoInscriptionRepository;
        $this->echeancierRepository = $echeancierRepository;
        $this->moyenneMatiereRepository = $moyenneMatiereRepository;
        $this->matiereUeRepository = $matiereUeRepository;
        $this->matiereRepository = $matiereRepository;
        $this->classeRepository = $classeRepository;
        $this->semestreRepository = $semestreRepository;
        $this->sessionRepository = $sessionRepository;
        $this->noteRepository = $noteRepository;
        $this->coursRepository = $coursRepository;

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


    public function gestionNotes($dataNotes, $groupeTypes, $data = [], $controleVefication, $controle): int
    {
        $compteIfNoteSuperieurMax = 0;
        foreach ($dataNotes as $key => $row) {
            $somme = 0;
            $coef = 0;
            foreach ($row->getValeurNotes() as $key1 => $value) {
                $nbreTour = 0;
                foreach ($groupeTypes as $key => $groupe) {
                    //$note = 0;
                    if ($key1 == $key) {

                        $note = (int)$groupe->getCoef() == 10 ? $value->getNote() * 2 * (int)$groupe->getType()->getCoef() : $value->getNote() * (int)$groupe->getType()->getCoef();

                        if ($value->getNote() > 10 && $groupe->getCoef() == 10) {
                            $compteIfNoteSuperieurMax++;
                        }
                    }
                    if ($groupe->getType())
                        $coef = $coef + (int)$groupe->getType()->getCoef();
                    $nbreTour++;
                }

                $somme = $somme + $note;
                // dd()

            }
            //dd($somme / ($coef / 2), $note, $coef);
            $moyenneEtudiant = $somme / ($nbreTour == 1 ? $coef : $coef / 2);
            $row->setMoyenneMatiere($moyenneEtudiant);

            $moyenneMatiere = $this->moyenneMatiereRepository->findOneBy(['matiere' => $data['matiere'], 'etudiant' => $row->getEtudiant()]);
            if ($moyenneMatiere) {
                $moyenneMatiere->setMoyenne($moyenneEtudiant);
                $matiereUeValide = $this->matiereUeRepository->findOneBy(['matiere' => $data['matiere']]);
                $moyenneMatiere->setValide($moyenneEtudiant  >= $matiereUeValide->getMoyenneValidation() ? 'Oui' : 'Non');
                $this->em->persist($moyenneMatiere);
                $this->em->flush();
            } else {
                $newMoyenneMatiere = new MoyenneMatiere();

                $newMoyenneMatiere->setEtudiant($row->getEtudiant());
                $newMoyenneMatiere->setMatiere($this->matiereRepository->find($data['matiere']));
                $newMoyenneMatiere->setMoyenne($moyenneEtudiant);

                $matiereUeValide = $this->matiereUeRepository->findOneBy(['matiere' => $data['matiere']]);

                $newMoyenneMatiere->setValide($moyenneEtudiant  >= $matiereUeValide->getMoyenneValidation() ? 'Oui' : 'Non');
                $newMoyenneMatiere->setSession($this->sessionRepository->find($data['session']));
                $this->em->persist($newMoyenneMatiere);
                $this->em->flush();
            }
        }

        if ($controleVefication) {

            //dd($controleVefication);
            $controleVefication->setCour($this->coursRepository->findOneBy(['classe' => $data['classe'], 'matiere' => $data['matiere'], 'anneeScolaire' => $this->semestreRepository->find($data['semestre'])->getAnneeScolaire()->getId()]));
            $controleVefication->setAnneeScolaire($this->semestreRepository->find($data['semestre'])->getAnneeScolaire());
            $controleVefication->setClasse($this->classeRepository->find($data['classe']));
            $controleVefication->setMatiere($this->matiereRepository->find($data['matiere']));
            $controleVefication->setSession($this->sessionRepository->find($data['session']));
            $controleVefication->setSemestre($this->semestreRepository->find($data['semestre']));
            $this->em->persist($controleVefication);
        } else {
            $controle->setCour($this->coursRepository->findOneBy(['classe' => $data['classe'], 'matiere' => $data['matiere'], 'anneeScolaire' => $this->semestreRepository->find($data['semestre'])->getAnneeScolaire()->getId()]));
            $controle->setAnneeScolaire($this->semestreRepository->find($data['semestre'])->getAnneeScolaire());
            $controle->setMatiere($this->matiereRepository->find($data['matiere']));
            $controle->setClasse($this->classeRepository->find($data['classe']));
            $controle->setSession($this->sessionRepository->find($data['session']));
            $controle->setSemestre($this->semestreRepository->find($data['semestre']));
            $this->em->persist($controle);
        }
        $this->em->flush();

        return $compteIfNoteSuperieurMax;
    }

    public function rangExposant($dataNotes,)
    {
        $tableau = [];

        foreach ($dataNotes as $allNotes) {

            $tableau[$allNotes->getEtudiant()->getId()] = (int)$allNotes->getMoyenneMatiere();
        }

        foreach ($dataNotes as  $allNotes) {

            foreach ($tableau as $key => $value) {

                // dd($key, $tableau[$allNotes->getEtudiant()->getId()] ==);
                if ($tableau[$allNotes->getEtudiant()->getId()] == $tableau[$key]) {
                    $rang = $this->Rangeleve($key, $tableau, count($tableau));

                    //je verifie si le existe deja afin de savoir sil est execo ou pas
                    $existeRang = $this->noteRepository->findBy(['controle' => $allNotes->getControle(), 'rang' => $rang]);
                    //dd($existeRang);

                    $note = $this->noteRepository->find($allNotes->getId());

                    if ($note) {
                        $note->setRang($rang);
                        if (count($existeRang) > 1) {
                            $note->setExposant("ex");
                        } else {
                            if ($rang == 1) {
                                $note->setExposant("er");
                            } else {
                                $note->setExposant("e");
                            }
                        }
                        $this->em->persist($note);
                        $this->em->flush();
                    } else {
                        if (count($existeRang) > 1) {
                            $allNotes->setExposant("ex");
                        } else {
                            if ($rang == 1) {
                                $allNotes->setExposant("er");
                            } else {
                                $allNotes->setExposant("e");
                            }
                        }
                    }
                }
            }

            /*  $existeRang = $noteRepository->findBy(['controle' => $allNotes->getControle(), 'rang' => $rang]);

                    if ($note) {
                        if (count($existeRang) > 1) {
                            $note->setExposant("ex");
                        } else {
                            if ($rang == 1) {
                                $note->setExposant("er");
                            } else {
                                $note->setExposant("e");
                            }
                        }
                        $entityManager->persist($note);
                        $entityManager->flush();
                    } */
        }
    }

    function Rangeleve($case, $tab, $Nbr)
    {
        $rang = 1;

        foreach ($tab as $key => $value) {
            if ($value > $tab[$case]) {
                $rang = $rang + 1;
            }
        }
        /*   for ($i = 1; $i < $Nbr; $i++) {
            if ($tab[$i] > $tab[$case]) {
                $rang = $rang + 1;
            }
        } */
        return $rang;
    }
}

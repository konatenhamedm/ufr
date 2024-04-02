<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    private ?NiveauEtudiant $niveauEtudiant = null;

    #[ORM\Column(length: 255)]
    private ?string $codeUtilisateur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\ManyToOne]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne]
    private ?Niveau $niveau = null;

    #[ORM\OneToMany(mappedBy: 'inscription', targetEntity: FraisInscription::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $fraisInscriptions;

    #[ORM\OneToMany(mappedBy: 'inscription', targetEntity: Echeancier::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $echeanciers;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\OneToMany(mappedBy: 'inscription', targetEntity: InfoInscription::class)]
    private Collection $infoInscriptions;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Utilisateur $caissiere = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $totalPaye = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    private ?Classe $classe = null;

    public function __construct()
    {
        $this->fraisInscriptions = new ArrayCollection();
        $this->dateInscription = new \DateTime();
        $this->echeanciers = new ArrayCollection();
        $this->infoInscriptions = new ArrayCollection();
        $this->totalPaye = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCaissiere(): ?Utilisateur
    {
        return $this->caissiere;
    }

    public function setCaissiere(?Utilisateur $caissiere): static
    {
        $this->caissiere = $caissiere;

        return $this;
    }



    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getNiveauEtudiant(): ?NiveauEtudiant
    {
        return $this->niveauEtudiant;
    }

    public function setNiveauEtudiant(?NiveauEtudiant $niveauEtudiant): static
    {
        $this->niveauEtudiant = $niveauEtudiant;

        return $this;
    }

    public function getCodeUtilisateur(): ?string
    {
        return $this->codeUtilisateur;
    }

    public function setCodeUtilisateur(string $codeUtilisateur): static
    {
        $this->codeUtilisateur = $codeUtilisateur;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeInterface $datePaiement): static
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * @return Collection<int, FraisInscription>
     */
    public function getFraisInscriptions(): Collection
    {
        return $this->fraisInscriptions;
    }

    public function addFraisInscription(FraisInscription $fraisInscription): static
    {
        if (!$this->fraisInscriptions->contains($fraisInscription)) {
            $this->fraisInscriptions->add($fraisInscription);
            $fraisInscription->setInscription($this);
        }

        return $this;
    }

    public function removeFraisInscription(FraisInscription $fraisInscription): static
    {
        if ($this->fraisInscriptions->removeElement($fraisInscription)) {
            // set the owning side to null (unless already changed)
            if ($fraisInscription->getInscription() === $this) {
                $fraisInscription->setInscription(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Echeancier>
     */
    public function getEcheanciers(): Collection
    {
        return $this->echeanciers;
    }

    public function addEcheancier(Echeancier $echeancier): static
    {
        if (!$this->echeanciers->contains($echeancier)) {
            $this->echeanciers->add($echeancier);
            $echeancier->setInscription($this);
        }

        return $this;
    }

    public function removeEcheancier(Echeancier $echeancier): static
    {
        if ($this->echeanciers->removeElement($echeancier)) {
            // set the owning side to null (unless already changed)
            if ($echeancier->getInscription() === $this) {
                $echeancier->setInscription(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, InfoInscription>
     */
    public function getInfoInscriptions(): Collection
    {
        return $this->infoInscriptions;
    }

    public function addInfoInscription(InfoInscription $infoInscription): static
    {
        if (!$this->infoInscriptions->contains($infoInscription)) {
            $this->infoInscriptions->add($infoInscription);
            $infoInscription->setInscription($this);
        }

        return $this;
    }

    public function removeInfoInscription(InfoInscription $infoInscription): static
    {
        if ($this->infoInscriptions->removeElement($infoInscription)) {
            // set the owning side to null (unless already changed)
            if ($infoInscription->getInscription() === $this) {
                $infoInscription->setInscription(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getTotalPaye(): ?string
    {
        return $this->totalPaye;
    }

    public function setTotalPaye(?string $totalPaye): static
    {
        $this->totalPaye = $totalPaye;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

        return $this;
    }
}

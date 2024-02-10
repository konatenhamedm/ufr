<?php

namespace App\Entity;

use App\Repository\PreinscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PreinscriptionRepository::class)]
#[Table(name: 'compta_preinscription')]
/* #[UniqueEntity(fields: ['niveau', 'etudiant'], message: 'Vous avez déjà une demande de préinscription')] */
class Preinscription
{
    const ETATS = [
        'cree' => 'En attente de validation',
        'attente_paiement' => 'En attente de paiement',
        'valide' => 'Validées',
        'rejet' => 'Rejetées',
        'attente_inscription' => 'En attente de validation inscription',
        'inscription' => 'Inscription Validées'
    ];


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $datePreinscription = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez choisir un niveau')]
    private ?Niveau $niveau = null;

    #[ORM\ManyToOne(inversedBy: 'preinscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(length: 30)]
    private ?string $etat = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner un motif de refus', groups: ['rejet-preinscription'])]
    private ?string $motif;

    #[ORM\OneToOne(mappedBy: 'preinscription', cascade: ['persist', 'remove'])]
    private ?InfoPreinscription $infoPreinscription = null;

    #[ORM\OneToOne(mappedBy: 'preinscription', cascade: ['persist', 'remove'])]
    private ?DeliberationPreinscription $deliberation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\ManyToOne]
    private ?Utilisateur $caissiere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etatDeliberation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePreinscription(): ?\DateTimeInterface
    {
        return $this->datePreinscription;
    }

    public function setDatePreinscription(\DateTimeInterface $datePreinscription): static
    {
        $this->datePreinscription = $datePreinscription;

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

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }



    /**
     * Get the value of motif
     */
    public function getMotif(): ?string
    {
        return $this->motif;
    }

    /**
     * Set the value of motif
     */
    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getInfoPreinscription(): ?InfoPreinscription
    {
        return $this->infoPreinscription;
    }

    public function setInfoPreinscription(InfoPreinscription $infoPreinscription): static
    {
        // set the owning side of the relation if necessary
        if ($infoPreinscription->getPreinscription() !== $this) {
            $infoPreinscription->setPreinscription($this);
        }

        $this->infoPreinscription = $infoPreinscription;

        return $this;
    }


    public function getFiliere()
    {
        return $this->getNiveau()->getFiliere();
    }

    public function getDeliberation(): ?DeliberationPreinscription
    {
        return $this->deliberation;
    }

    public function setDeliberation(DeliberationPreinscription $deliberation): static
    {
        // set the owning side of the relation if necessary
        if ($deliberation->getPreinscription() !== $this) {
            $deliberation->setPreinscription($this);
        }

        $this->deliberation = $deliberation;

        return $this;
    }

    public function getNomComplet()
    {
        return $this->getEtudiant()->getNomComplet();
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
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

    public function getEtatDeliberation(): ?string
    {
        return $this->etatDeliberation;
    }

    public function setEtatDeliberation(?string $etatDeliberation): static
    {
        $this->etatDeliberation = $etatDeliberation;

        return $this;
    }

    public function getDateValidation(): ?\DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTimeInterface $dateValidation): static
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}

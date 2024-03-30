<?php

namespace App\Entity;

use App\Repository\InfoInscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InfoInscriptionRepository::class)]
#[Table(name: 'compta_info_inscription')]
class InfoInscription
{
    const ETATS = [
        'payer' => 'payer',
        'attente_confirmation' => 'Attente Confirmation',
        'confirmer' => 'Confirmation valider',
        'annuler' => 'Annuler',

    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    #[Assert\Positive(message: 'Le montant payé doit être > à 0')]
    private ?string $montant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne]
    private ?Utilisateur $caissiere = null;

    #[ORM\ManyToOne(inversedBy: 'infoInscriptions', cascade: ['persist', 'remove'])]
    private ?Inscription $inscription = null;

    #[ORM\ManyToOne(inversedBy: 'infoInscriptions')]
    private ?NaturePaiement $modePaiement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'infoInscriptions')]
    private ?Echeancier $echenacier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $banque = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCheque = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tireur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contact = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numeroCheque = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCredit = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getCaissiere(): ?Utilisateur
    {
        return $this->caissiere;
    }

    public function setCaissiere(?Utilisateur $caissiere): static
    {
        $this->caissiere = $caissiere;

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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): static
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getModePaiement(): ?NaturePaiement
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?NaturePaiement $modePaiement): static
    {
        $this->modePaiement = $modePaiement;

        return $this;
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

    public function getEchenacier(): ?Echeancier
    {
        return $this->echenacier;
    }

    public function setEchenacier(?Echeancier $echenacier): static
    {
        $this->echenacier = $echenacier;

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

    public function getBanque(): ?string
    {
        return $this->banque;
    }

    public function setBanque(?string $banque): static
    {
        $this->banque = $banque;

        return $this;
    }

    public function getDateCheque(): ?\DateTimeInterface
    {
        return $this->dateCheque;
    }

    public function setDateCheque(?\DateTimeInterface $dateCheque): static
    {
        $this->dateCheque = $dateCheque;

        return $this;
    }

    public function getTireur(): ?string
    {
        return $this->tireur;
    }

    public function setTireur(?string $tireur): static
    {
        $this->tireur = $tireur;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function getNumeroCheque(): ?string
    {
        return $this->numeroCheque;
    }

    public function setNumeroCheque(?string $numeroCheque): static
    {
        $this->numeroCheque = $numeroCheque;

        return $this;
    }

    public function getDateCredit(): ?\DateTimeInterface
    {
        return $this->dateCredit;
    }

    public function setDateCredit(?\DateTimeInterface $dateCredit): static
    {
        $this->dateCredit = $dateCredit;

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
}

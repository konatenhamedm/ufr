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
}

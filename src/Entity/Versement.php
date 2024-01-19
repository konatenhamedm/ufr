<?php

namespace App\Entity;

use App\Repository\VersementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VersementRepository::class)]
#[Table(name: 'compta_versement')]
class Versement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'Veuillez renseigner la date de versement')]
    private ?\DateTimeInterface $dateVersement = null;

   

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    #[Assert\Positive(message: 'Le montant doit être > à 0')]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'versements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner le type de frais')]
    private ?FraisInscription $fraisInscription = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?NaturePaiement $nature = null;

    #[ORM\Column(length: 11)]
    private ?string $reference = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateVersement(): ?\DateTimeInterface
    {
        return $this->dateVersement;
    }

    public function setDateVersement(\DateTimeInterface $dateVersement): static
    {
        $this->dateVersement = $dateVersement;

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

    public function getFraisInscription(): ?FraisInscription
    {
        return $this->fraisInscription;
    }

    public function setFraisInscription(?FraisInscription $fraisInscription): static
    {
        $this->fraisInscription = $fraisInscription;

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

    public function getNature(): ?NaturePaiement
    {
        return $this->nature;
    }

    public function setNature(?NaturePaiement $nature): static
    {
        $this->nature = $nature;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }
}

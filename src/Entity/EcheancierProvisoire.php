<?php

namespace App\Entity;

use App\Repository\EcheancierProvisoireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcheancierProvisoireRepository::class)]
class EcheancierProvisoire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateVersement = null;

    #[ORM\Column(length: 255)]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'echeancierProvisoires')]
    private ?BlocEcheancier $blocEcheancier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
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

    public function getBlocEcheancier(): ?BlocEcheancier
    {
        return $this->blocEcheancier;
    }

    public function setBlocEcheancier(?BlocEcheancier $blocEcheancier): static
    {
        $this->blocEcheancier = $blocEcheancier;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\CursusProfessionnelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CursusProfessionnelRepository::class)]
class CursusProfessionnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $emploi = null;

    #[ORM\Column(length: 255)]
    private ?string $employeur = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255)]
    private ?string $activite = null;

    #[ORM\ManyToOne(inversedBy: 'cursusProfessionnels')]
    private ?Etudiant $etudiant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmploi(): ?string
    {
        return $this->emploi;
    }

    public function setEmploi(string $emploi): static
    {
        $this->emploi = $emploi;

        return $this;
    }

    public function getEmployeur(): ?string
    {
        return $this->employeur;
    }

    public function setEmployeur(string $employeur): static
    {
        $this->employeur = $employeur;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface  $dateDebut): static
    {
        $this->dateDebut = $dateDebut;


        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getActivite(): ?string
    {
        return $this->activite;
    }

    public function setActivite(string $activite): static
    {
        $this->activite = $activite;

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
}

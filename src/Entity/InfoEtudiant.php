<?php

namespace App\Entity;

use App\Repository\InfoEtudiantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfoEtudiantRepository::class)]
class InfoEtudiant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $habiteAvec = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tuteurNomPrenoms = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tuteurFonction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tuteurContact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tuteurDomicile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tuteurEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $corresNomPrenoms = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $corresFonction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $corresContacts = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $corresDomicile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $corresEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pereNomPrenoms = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pereFonction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pereContacts = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pereDomicile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mereNomPrenoms = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mereFonction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mereContacts = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mereDomicile = null;

    #[ORM\ManyToOne(inversedBy: 'infoEtudiants')]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pereEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mereEmail = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHabiteAvec(): ?string
    {
        return $this->habiteAvec;
    }

    public function setHabiteAvec(?string $habiteAvec): static
    {
        $this->habiteAvec = $habiteAvec;

        return $this;
    }

    public function getTuteurNomPrenoms(): ?string
    {
        return $this->tuteurNomPrenoms;
    }

    public function setTuteurNomPrenoms(?string $tuteurNomPrenoms): static
    {
        $this->tuteurNomPrenoms = $tuteurNomPrenoms;

        return $this;
    }

    public function getTuteurFonction(): ?string
    {
        return $this->tuteurFonction;
    }

    public function setTuteurFonction(?string $tuteurFonction): static
    {
        $this->tuteurFonction = $tuteurFonction;

        return $this;
    }

    public function getTuteurContact(): ?string
    {
        return $this->tuteurContact;
    }

    public function setTuteurContact(?string $tuteurContact): static
    {
        $this->tuteurContact = $tuteurContact;

        return $this;
    }

    public function getTuteurDomicile(): ?string
    {
        return $this->tuteurDomicile;
    }

    public function setTuteurDomicile(?string $tuteurDomicile): static
    {
        $this->tuteurDomicile = $tuteurDomicile;

        return $this;
    }

    public function getTuteurEmail(): ?string
    {
        return $this->tuteurEmail;
    }

    public function setTuteurEmail(?string $tuteurEmail): static
    {
        $this->tuteurEmail = $tuteurEmail;

        return $this;
    }

    public function getCorresNomPrenoms(): ?string
    {
        return $this->corresNomPrenoms;
    }

    public function setCorresNomPrenoms(?string $corresNomPrenoms): static
    {
        $this->corresNomPrenoms = $corresNomPrenoms;

        return $this;
    }

    public function getCorresFonction(): ?string
    {
        return $this->corresFonction;
    }

    public function setCorresFonction(?string $corresFonction): static
    {
        $this->corresFonction = $corresFonction;

        return $this;
    }

    public function getCorresContacts(): ?string
    {
        return $this->corresContacts;
    }

    public function setCorresContacts(?string $corresContacts): static
    {
        $this->corresContacts = $corresContacts;

        return $this;
    }

    public function getCorresDomicile(): ?string
    {
        return $this->corresDomicile;
    }

    public function setCorresDomicile(?string $corresDomicile): static
    {
        $this->corresDomicile = $corresDomicile;

        return $this;
    }

    public function getCorresEmail(): ?string
    {
        return $this->corresEmail;
    }

    public function setCorresEmail(?string $corresEmail): static
    {
        $this->corresEmail = $corresEmail;

        return $this;
    }

    public function getPereNomPrenoms(): ?string
    {
        return $this->pereNomPrenoms;
    }

    public function setPereNomPrenoms(?string $pereNomPrenoms): static
    {
        $this->pereNomPrenoms = $pereNomPrenoms;

        return $this;
    }

    public function getPereFonction(): ?string
    {
        return $this->pereFonction;
    }

    public function setPereFonction(?string $pereFonction): static
    {
        $this->pereFonction = $pereFonction;

        return $this;
    }

    public function getPereContacts(): ?string
    {
        return $this->pereContacts;
    }

    public function setPereContacts(?string $pereContacts): static
    {
        $this->pereContacts = $pereContacts;

        return $this;
    }

    public function getPereDomicile(): ?string
    {
        return $this->pereDomicile;
    }

    public function setPereDomicile(?string $pereDomicile): static
    {
        $this->pereDomicile = $pereDomicile;

        return $this;
    }

    public function getMereNomPrenoms(): ?string
    {
        return $this->mereNomPrenoms;
    }

    public function setMereNomPrenoms(?string $mereNomPrenoms): static
    {
        $this->mereNomPrenoms = $mereNomPrenoms;

        return $this;
    }

    public function getMereFonction(): ?string
    {
        return $this->mereFonction;
    }

    public function setMereFonction(?string $mereFonction): static
    {
        $this->mereFonction = $mereFonction;

        return $this;
    }

    public function getMereContacts(): ?string
    {
        return $this->mereContacts;
    }

    public function setMereContacts(?string $mereContacts): static
    {
        $this->mereContacts = $mereContacts;

        return $this;
    }

    public function getMereDomicile(): ?string
    {
        return $this->mereDomicile;
    }

    public function setMereDomicile(?string $mereDomicile): static
    {
        $this->mereDomicile = $mereDomicile;

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

    public function getPereEmail(): ?string
    {
        return $this->pereEmail;
    }

    public function setPereEmail(?string $pereEmail): static
    {
        $this->pereEmail = $pereEmail;

        return $this;
    }

    public function getMereEmail(): ?string
    {
        return $this->mereEmail;
    }

    public function setMereEmail(?string $mereEmail): static
    {
        $this->mereEmail = $mereEmail;

        return $this;
    }
}

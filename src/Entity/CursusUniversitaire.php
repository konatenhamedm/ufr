<?php

namespace App\Entity;

use App\Repository\CursusUniversitaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CursusUniversitaireRepository::class)]
class CursusUniversitaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etablissement = null;

    #[ORM\Column(length: 255)]
    private ?string $annee = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $pays = null;

    #[ORM\Column(length: 255)]
    private ?string $diplome = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mention = null;

    #[ORM\ManyToOne(inversedBy: 'cursusUniversitaires')]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un numéro diplome')]
    private ?string $numeroDiplome = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroMatricule = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $bac = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $releve = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setBac(?Fichier $bac): static
    {
        $this->bac = $bac;
        return $this;
    }

    public function getBac(): ?Fichier
    {
        return $this->bac;
    }

    public function setReleve(?Fichier $releve): static
    {
        $this->releve = $releve;
        return $this;
    }

    public function getReleve(): ?Fichier
    {
        return $this->releve;
    }

    public function getEtablissement(): ?string
    {
        return $this->etablissement;
    }

    public function setEtablissement(string $etablissement): static
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getDiplome(): ?string
    {
        return $this->diplome;
    }

    public function setDiplome(string $diplome): static
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(string $mention): static
    {
        $this->mention = $mention;

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

    public function getNumeroDiplome(): ?string
    {
        return $this->numeroDiplome;
    }

    public function setNumeroDiplome(string $numeroDiplome): static
    {
        $this->numeroDiplome = $numeroDiplome;

        return $this;
    }

    public function getNumeroMatricule(): ?string
    {
        return $this->numeroMatricule;
    }

    public function setNumeroMatricule(string $numeroMatricule): static
    {
        $this->numeroMatricule = $numeroMatricule;

        return $this;
    }
}

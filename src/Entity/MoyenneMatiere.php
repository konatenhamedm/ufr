<?php

namespace App\Entity;

use App\Repository\MoyenneMatiereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MoyenneMatiereRepository::class)]
class MoyenneMatiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'moyenneMatieres')]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'moyenneMatieres')]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'moyenneMatieres')]
    private ?Session $session = null;

    #[ORM\Column(length: 255)]
    private ?string $moyenne = null;

    #[ORM\Column(length: 255)]
    private ?string $valide = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

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

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getMoyenne(): ?string
    {
        return $this->moyenne;
    }

    public function setMoyenne(string $moyenne): static
    {
        $this->moyenne = $moyenne;

        return $this;
    }

    public function getValide(): ?string
    {
        return $this->valide;
    }

    public function setValide(string $valide): static
    {
        $this->valide = $valide;

        return $this;
    }
}

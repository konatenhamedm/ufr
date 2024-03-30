<?php

namespace App\Entity;

use App\Repository\MatiereUeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatiereUeRepository::class)]
class MatiereUe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'matiereUes')]
    private ?UniteEnseignement $uniteEnseignement = null;

    #[ORM\ManyToOne(inversedBy: 'matiereUes')]
    private ?Matiere $matiere = null;

    #[ORM\Column]
    private ?int $coef = null;



    #[ORM\Column]
    private ?int $nombreCredit = null;

    #[ORM\Column(length: 255)]
    private ?string $noteEliminatoire = null;

    #[ORM\Column(length: 255)]
    private ?string $moyenneValidation = null;

    #[ORM\Column(options: ["default" => "true"])]
    private ?bool $visible = null;


    public function __construct()
    {
        $this->visible = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniteEnseignement(): ?UniteEnseignement
    {
        return $this->uniteEnseignement;
    }

    public function setUniteEnseignement(?UniteEnseignement $uniteEnseignement): static
    {
        $this->uniteEnseignement = $uniteEnseignement;

        return $this;
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

    public function getCoef(): ?int
    {
        return $this->coef;
    }

    public function setCoef(int $coef): static
    {
        $this->coef = $coef;

        return $this;
    }



    public function getNombreCredit(): ?int
    {
        return $this->nombreCredit;
    }

    public function setNombreCredit(int $nombreCredit): static
    {
        $this->nombreCredit = $nombreCredit;

        return $this;
    }

    public function getNoteEliminatoire(): ?string
    {
        return $this->noteEliminatoire;
    }

    public function setNoteEliminatoire(string $noteEliminatoire): static
    {
        $this->noteEliminatoire = $noteEliminatoire;

        return $this;
    }

    public function getMoyenneValidation(): ?string
    {
        return $this->moyenneValidation;
    }

    public function setMoyenneValidation(string $moyenneValidation): static
    {
        $this->moyenneValidation = $moyenneValidation;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

        return $this;
    }
}

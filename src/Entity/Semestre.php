<?php

namespace App\Entity;

use App\Repository\SemestreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SemestreRepository::class)]
class Semestre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'semestres')]
    private ?AnneeScolaire $anneeScolaire = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;


    #[ORM\Column]
    private ?int $coef = null;

    #[ORM\Column(length: 255)]
    private ?string $bloque = null;

    #[ORM\OneToMany(mappedBy: 'semestre', targetEntity: UniteEnseignement::class)]
    private Collection $uniteEnseignements;

    #[ORM\OneToMany(mappedBy: 'semestre', targetEntity: Controle::class)]
    private Collection $controles;

    #[ORM\Column]
    private ?bool $actif = null;

    public function __construct()
    {
        $this->uniteEnseignements = new ArrayCollection();
        $this->controles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getAnneeScolaire(): ?AnneeScolaire
    {
        return $this->anneeScolaire;
    }

    public function setAnneeScolaire(?AnneeScolaire $anneeScolaire): static
    {
        $this->anneeScolaire = $anneeScolaire;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
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


    public function getCoef(): ?int
    {
        return $this->coef;
    }

    public function setCoef(int $coef): static
    {
        $this->coef = $coef;

        return $this;
    }

    public function getBloque(): ?string
    {
        return $this->bloque;
    }

    public function setBloque(string $bloque): static
    {
        $this->bloque = $bloque;

        return $this;
    }

    /**
     * @return Collection<int, UniteEnseignement>
     */
    public function getUniteEnseignements(): Collection
    {
        return $this->uniteEnseignements;
    }

    public function addUniteEnseignement(UniteEnseignement $uniteEnseignement): static
    {
        if (!$this->uniteEnseignements->contains($uniteEnseignement)) {
            $this->uniteEnseignements->add($uniteEnseignement);
            $uniteEnseignement->setSemestre($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): static
    {
        if ($this->uniteEnseignements->removeElement($uniteEnseignement)) {
            // set the owning side to null (unless already changed)
            if ($uniteEnseignement->getSemestre() === $this) {
                $uniteEnseignement->setSemestre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Controle>
     */
    public function getControles(): Collection
    {
        return $this->controles;
    }

    public function addControle(Controle $controle): static
    {
        if (!$this->controles->contains($controle)) {
            $this->controles->add($controle);
            $controle->setSemestre($this);
        }

        return $this;
    }

    public function removeControle(Controle $controle): static
    {
        if ($this->controles->removeElement($controle)) {
            // set the owning side to null (unless already changed)
            if ($controle->getSemestre() === $this) {
                $controle->setSemestre(null);
            }
        }

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }
}

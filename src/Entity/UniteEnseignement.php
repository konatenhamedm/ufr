<?php

namespace App\Entity;

use App\Repository\UniteEnseignementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UniteEnseignementRepository::class)]
class UniteEnseignement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'uniteEnseignements')]
    private ?Semestre $semestre = null;

    #[ORM\Column(length: 255)]
    private ?string $codeUe = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $coef = null;

    #[ORM\Column(length: 255)]
    private ?string $attribut = null;

    #[ORM\Column]
    private ?int $volumeHoraire = null;

    #[ORM\Column]
    private ?int $totalCredit = null;

    #[ORM\ManyToOne(inversedBy: 'uniteEnseignements')]
    private ?Niveau $niveau = null;

    #[ORM\OneToMany(mappedBy: 'uniteEnseignement', targetEntity: MatiereUe::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $matiereUes;

    public function __construct()
    {
        $this->matiereUes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(?Semestre $semestre): static
    {
        $this->semestre = $semestre;

        return $this;
    }

    public function getCodeUe(): ?string
    {
        return $this->codeUe;
    }

    public function setCodeUe(string $codeUe): static
    {
        $this->codeUe = $codeUe;

        return $this;
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

    public function getCoef(): ?string
    {
        return $this->coef;
    }

    public function setCoef(string $coef): static
    {
        $this->coef = $coef;

        return $this;
    }

    public function getAttribut(): ?string
    {
        return $this->attribut;
    }

    public function setAttribut(string $attribut): static
    {
        $this->attribut = $attribut;

        return $this;
    }

    public function getVolumeHoraire(): ?int
    {
        return $this->volumeHoraire;
    }

    public function setVolumeHoraire(int $volumeHoraire): static
    {
        $this->volumeHoraire = $volumeHoraire;

        return $this;
    }

    public function getTotalCredit(): ?int
    {
        return $this->totalCredit;
    }

    public function setTotalCredit(int $totalCredit): static
    {
        $this->totalCredit = $totalCredit;

        return $this;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * @return Collection<int, MatiereUe>
     */
    public function getMatiereUes(): Collection
    {
        return $this->matiereUes;
    }

    public function addMatiereUe(MatiereUe $matiereUe): static
    {
        if (!$this->matiereUes->contains($matiereUe)) {
            $this->matiereUes->add($matiereUe);
            $matiereUe->setUniteEnseignement($this);
        }

        return $this;
    }

    public function removeMatiereUe(MatiereUe $matiereUe): static
    {
        if ($this->matiereUes->removeElement($matiereUe)) {
            // set the owning side to null (unless already changed)
            if ($matiereUe->getUniteEnseignement() === $this) {
                $matiereUe->setUniteEnseignement(null);
            }
        }

        return $this;
    }
}

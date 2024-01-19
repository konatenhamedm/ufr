<?php

namespace App\Entity;

use App\Repository\FiliereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FiliereRepository::class)]
#[Table(name: 'param_filiere')]
#[UniqueEntity('code', message: 'Ce code est déjà utilisé')]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un code')]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un libellé')]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Veuillez renseigner une description')]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'filiere', targetEntity: Niveau::class)]
    private Collection $niveaux;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    private ?string $montantPreinscription = null;

    #[ORM\OneToMany(mappedBy: 'filiere', targetEntity: NiveauEtudiant::class)]
    private Collection $niveauEtudiants;

    public function __construct()
    {
        $this->niveaux = new ArrayCollection();
        $this->niveauEtudiants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Niveau>
     */
    public function getNiveaux(): Collection
    {
        return $this->niveaux;
    }

    public function addNiveau(Niveau $niveau): static
    {
        if (!$this->niveaux->contains($niveau)) {
            $this->niveaux->add($niveau);
            $niveau->setFiliere($this);
        }

        return $this;
    }

    public function removeNiveau(Niveau $niveau): static
    {
        if ($this->niveaux->removeElement($niveau)) {
            // set the owning side to null (unless already changed)
            if ($niveau->getFiliere() === $this) {
                $niveau->setFiliere(null);
            }
        }

        return $this;
    }

    public function getMontantPreinscription(): ?string
    {
        return $this->montantPreinscription;
    }

    public function setMontantPreinscription(string $montantPreinscription): static
    {
        $this->montantPreinscription = $montantPreinscription;

        return $this;
    }

    /**
     * @return Collection<int, NiveauEtudiant>
     */
    public function getNiveauEtudiants(): Collection
    {
        return $this->niveauEtudiants;
    }

    public function addNiveauEtudiant(NiveauEtudiant $niveauEtudiant): static
    {
        if (!$this->niveauEtudiants->contains($niveauEtudiant)) {
            $this->niveauEtudiants->add($niveauEtudiant);
            $niveauEtudiant->setFiliere($this);
        }

        return $this;
    }

    public function removeNiveauEtudiant(NiveauEtudiant $niveauEtudiant): static
    {
        if ($this->niveauEtudiants->removeElement($niveauEtudiant)) {
            // set the owning side to null (unless already changed)
            if ($niveauEtudiant->getFiliere() === $this) {
                $niveauEtudiant->setFiliere(null);
            }
        }

        return $this;
    }
}

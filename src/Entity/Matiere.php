<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
#[Table(name: 'gestion_matiere')]
#[UniqueEntity('code', message: 'Ce code est déjà utilisé')]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un code')]
    private ?string $code = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un numéro d\'ordre')]
    private ?int $ordre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez renseigner le libellé')]
    private ?string $libelle = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner le type de matière')]
    private ?TypeMatiere $typeMatiere = null;

    #[ORM\OneToMany(mappedBy: 'matiere', targetEntity: Cours::class)]
    private Collection $cours;

    #[ORM\OneToMany(mappedBy: 'matiere', targetEntity: MatiereUe::class)]
    private Collection $matiereUes;

    #[ORM\OneToMany(mappedBy: 'matiere', targetEntity: MoyenneMatiere::class)]
    private Collection $moyenneMatieres;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->matiereUes = new ArrayCollection();
        $this->moyenneMatieres = new ArrayCollection();
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

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

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

    public function getTypeMatiere(): ?TypeMatiere
    {
        return $this->typeMatiere;
    }

    public function setTypeMatiere(?TypeMatiere $typeMatiere): static
    {
        $this->typeMatiere = $typeMatiere;

        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): static
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setMatiere($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): static
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getMatiere() === $this) {
                $cour->setMatiere(null);
            }
        }

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
            $matiereUe->setMatiere($this);
        }

        return $this;
    }

    public function removeMatiereUe(MatiereUe $matiereUe): static
    {
        if ($this->matiereUes->removeElement($matiereUe)) {
            // set the owning side to null (unless already changed)
            if ($matiereUe->getMatiere() === $this) {
                $matiereUe->setMatiere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MoyenneMatiere>
     */
    public function getMoyenneMatieres(): Collection
    {
        return $this->moyenneMatieres;
    }

    public function addMoyenneMatiere(MoyenneMatiere $moyenneMatiere): static
    {
        if (!$this->moyenneMatieres->contains($moyenneMatiere)) {
            $this->moyenneMatieres->add($moyenneMatiere);
            $moyenneMatiere->setMatiere($this);
        }

        return $this;
    }

    public function removeMoyenneMatiere(MoyenneMatiere $moyenneMatiere): static
    {
        if ($this->moyenneMatieres->removeElement($moyenneMatiere)) {
            // set the owning side to null (unless already changed)
            if ($moyenneMatiere->getMatiere() === $this) {
                $moyenneMatiere->setMatiere(null);
            }
        }

        return $this;
    }
}

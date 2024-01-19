<?php

namespace App\Entity;

use App\Repository\ExamenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Unique;

#[ORM\Entity(repositoryClass: ExamenRepository::class)]
#[Table(name: 'param_examen')]
#[UniqueEntity('code', message: 'Ce code est déjà utilisé')]
class Examen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un libellé')]
    private ?string $libelle = null;

    #[ORM\Column(length: 10, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un code')]
    private ?string $code = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'Veuillez renseigner la date de début')]
    private ?\DateTimeInterface $dateExamen = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un niveau')]
    private ?Niveau $niveau = null;

    #[ORM\OneToMany(mappedBy: 'examen', targetEntity: MatiereExamen::class, orphanRemoval: true, cascade: ['persist'])]
    #[Assert\Valid()]
    private Collection $matiereExamens;

    public function __construct()
    {
        $this->matiereExamens = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDateExamen(): ?\DateTimeInterface
    {
        return $this->dateExamen;
    }

    public function setDateExamen(\DateTimeInterface $dateExamen): static
    {
        $this->dateExamen = $dateExamen;

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
     * @return Collection<int, MatiereExamen>
     */
    public function getMatiereExamens(): Collection
    {
        return $this->matiereExamens;
    }

    public function addMatiereExamen(MatiereExamen $matiereExamen): static
    {
        if (!$this->matiereExamens->contains($matiereExamen)) {
            $this->matiereExamens->add($matiereExamen);
            $matiereExamen->setExamen($this);
        }

        return $this;
    }

    public function removeMatiereExamen(MatiereExamen $matiereExamen): static
    {
        if ($this->matiereExamens->removeElement($matiereExamen)) {
            // set the owning side to null (unless already changed)
            if ($matiereExamen->getExamen() === $this) {
                $matiereExamen->setExamen(null);
            }
        }

        return $this;
    }
}

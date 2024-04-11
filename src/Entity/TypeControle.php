<?php

namespace App\Entity;

use App\Repository\TypeControleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeControleRepository::class)]
class TypeControle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $coef = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Controle::class)]
    private Collection $controles;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: GroupeType::class)]
    private Collection $groupeTypes;

    public function __construct()
    {
        $this->controles = new ArrayCollection();
        $this->groupeTypes = new ArrayCollection();
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

    public function getCoef(): ?string
    {
        return $this->coef;
    }

    public function setCoef(string $coef): static
    {
        $this->coef = $coef;

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
            $controle->setType($this);
        }

        return $this;
    }

    public function removeControle(Controle $controle): static
    {
        if ($this->controles->removeElement($controle)) {
            // set the owning side to null (unless already changed)
            if ($controle->getType() === $this) {
                $controle->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupeType>
     */
    public function getGroupeTypes(): Collection
    {
        return $this->groupeTypes;
    }

    public function addGroupeType(GroupeType $groupeType): static
    {
        if (!$this->groupeTypes->contains($groupeType)) {
            $this->groupeTypes->add($groupeType);
            $groupeType->setType($this);
        }

        return $this;
    }

    public function removeGroupeType(GroupeType $groupeType): static
    {
        if ($this->groupeTypes->removeElement($groupeType)) {
            // set the owning side to null (unless already changed)
            if ($groupeType->getType() === $this) {
                $groupeType->setType(null);
            }
        }

        return $this;
    }
}

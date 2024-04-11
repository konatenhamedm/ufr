<?php

namespace App\Entity;

use App\Repository\GroupeTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeTypeRepository::class)]
class GroupeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNote = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coef = null;

    #[ORM\ManyToOne(inversedBy: 'groupeTypes')]
    private ?Controle $controle = null;

    #[ORM\ManyToOne(inversedBy: 'groupeTypes')]
    private ?TypeControle $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateNote(): ?\DateTimeInterface
    {
        return $this->dateNote;
    }

    public function setDateNote(?\DateTimeInterface $dateNote): static
    {
        $this->dateNote = $dateNote;

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

    public function getControle(): ?Controle
    {
        return $this->controle;
    }

    public function setControle(?Controle $controle): static
    {
        $this->controle = $controle;

        return $this;
    }

    public function getType(): ?TypeControle
    {
        return $this->type;
    }

    public function setType(?TypeControle $type): static
    {
        $this->type = $type;

        return $this;
    }
}

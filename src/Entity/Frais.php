<?php

namespace App\Entity;

use App\Repository\FraisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: FraisRepository::class)]
#[Table(name: 'gestion_frais')]
class Frais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'veuillez sélectionner le type de frais')]
    private ?TypeFrais $typeFrais = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0')]
    #[Assert\PositiveOrZero(message: 'Le montant doit être > 0 pour chaque ligne', groups: ['niveau-frais'])]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'frais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Niveau $niveau = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeFrais(): ?TypeFrais
    {
        return $this->typeFrais;
    }

    public function setTypeFrais(?TypeFrais $typeFrais): static
    {
        $this->typeFrais = $typeFrais;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

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
}

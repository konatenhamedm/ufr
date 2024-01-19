<?php

namespace App\Entity;

use App\Repository\InfoNiveauRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: InfoNiveauRepository::class)]
#[Table(name: 'param_info_niveau')]
class InfoNiveau
{
    const NATURES  = [
        'Préinscription' => 'preinscription'
    ];
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $nature = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $details = null;

    #[ORM\ManyToOne(inversedBy: 'infoNiveaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Niveau $niveau = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(string $nature): static
    {
        $this->nature = $nature;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): static
    {
        $this->details = $details;

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

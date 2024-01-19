<?php

namespace App\Entity;

use App\Repository\LigneDeliberationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LigneDeliberationRepository::class)]
#[Table(name: 'dir_ligne_deliberation')]
class LigneDeliberation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneDeliberations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Deliberation $deliberation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    #[Assert\PositiveOrZero(message: 'La note doit être >= 0')]
    private ?string $note = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner une matière')]
    private ?MatiereExamen $matiereExamen = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $coefficient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliberation(): ?Deliberation
    {
        return $this->deliberation;
    }

    public function setDeliberation(?Deliberation $deliberation): static
    {
        $this->deliberation = $deliberation;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getMatiereExamen(): ?MatiereExamen
    {
        return $this->matiereExamen;
    }

    public function setMatiereExamen(?MatiereExamen $matiereExamen): static
    {
        $this->matiereExamen = $matiereExamen;

        return $this;
    }

    public function getCoefficient(): ?int
    {
        return $this->coefficient;
    }

    public function setCoefficient(int $coefficient): static
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    public function getMatiere()
    {
        return $this->getMatiereExamen()->getMatiere();
    }
}

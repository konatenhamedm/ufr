<?php

namespace App\Entity;

use App\Repository\DeliberationPreinscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeliberationPreinscriptionRepository::class)]
#[Table(name: 'dir_deliberation_preinscription')]
class DeliberationPreinscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'deliberation', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un candidat')]
    private ?Preinscription $preinscription = null;

    #[ORM\OneToOne(inversedBy: 'infoPreinscription', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Deliberation $deliberation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPreinscription(): ?Preinscription
    {
        return $this->preinscription;
    }

    public function setPreinscription(Preinscription $preinscription): static
    {
        $this->preinscription = $preinscription;

        return $this;
    }

    public function getDeliberation(): ?Deliberation
    {
        return $this->deliberation;
    }

    public function setDeliberation(Deliberation $deliberation): static
    {
        $this->deliberation = $deliberation;

        return $this;
    }
}

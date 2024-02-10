<?php

namespace App\Entity;

use App\Repository\EcheancierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcheancierRepository::class)]
class Echeancier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'echeanciers')]
    private ?Inscription $inscription = null;

    #[ORM\Column(length: 255)]
    private ?string $montant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    #[ORM\OneToMany(mappedBy: 'echenacier', targetEntity: InfoInscription::class)]
    private Collection $infoInscriptions;

    public function __construct()
    {
        $this->etat = "pas_payer";
        $this->infoInscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): static
    {
        $this->inscription = $inscription;

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, InfoInscription>
     */
    public function getInfoInscriptions(): Collection
    {
        return $this->infoInscriptions;
    }

    public function addInfoInscription(InfoInscription $infoInscription): static
    {
        if (!$this->infoInscriptions->contains($infoInscription)) {
            $this->infoInscriptions->add($infoInscription);
            $infoInscription->setEchenacier($this);
        }

        return $this;
    }

    public function removeInfoInscription(InfoInscription $infoInscription): static
    {
        if ($this->infoInscriptions->removeElement($infoInscription)) {
            // set the owning side to null (unless already changed)
            if ($infoInscription->getEchenacier() === $this) {
                $infoInscription->setEchenacier(null);
            }
        }

        return $this;
    }
}

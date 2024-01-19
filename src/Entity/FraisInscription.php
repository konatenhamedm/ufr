<?php

namespace App\Entity;

use App\Repository\FraisInscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: FraisInscriptionRepository::class)]
#[Table(name: 'gestion_frais_inscription_etudiant')]
class FraisInscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeFrais $typeFrais = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: '0')]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'fraisInscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Inscription $inscription = null;

    #[ORM\OneToMany(mappedBy: 'fraisInscription', targetEntity: Versement::class)]
    private Collection $versements;

    public function __construct()
    {
        $this->versements = new ArrayCollection();
    }

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

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): static
    {
        $this->inscription = $inscription;

        return $this;
    }

    /**
     * @return Collection<int, Versement>
     */
    public function getVersements(): Collection
    {
        return $this->versements;
    }

    public function addVersement(Versement $versement): static
    {
        if (!$this->versements->contains($versement)) {
            $this->versements->add($versement);
            $versement->setFraisInscription($this);
        }

        return $this;
    }

    public function removeVersement(Versement $versement): static
    {
        if ($this->versements->removeElement($versement)) {
            // set the owning side to null (unless already changed)
            if ($versement->getFraisInscription() === $this) {
                $versement->setFraisInscription(null);
            }
        }

        return $this;
    }


    public function getSolde()
    {
        return $this->getMontant() - $this->getTotal();
    }

    public function getTotal()
    {
        $montant = $this->getMontant();
        $totalVersement = 0;
        foreach ($this->getVersements() as $versement) {
            $totalVersement += $versement->getMontant();
        }
        return $totalVersement;
    }


    public function getLibelle()
    {
        return $this->getTypeFrais()->getLibelle();
    }
}

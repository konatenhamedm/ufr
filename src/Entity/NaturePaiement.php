<?php

namespace App\Entity;

use App\Repository\NaturePaiementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NaturePaiementRepository::class)]
#[Table(name: 'param_nature_paiement')]
#[UniqueEntity('code', message: 'Ce code est déjà utilisé')]
class NaturePaiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un code')]
    private ?string $code = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un libellé')]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?bool $confirmation = null;

    #[ORM\OneToMany(mappedBy: 'modePaiement', targetEntity: InfoPreinscription::class)]
    private Collection $infoPreinscriptions;

    #[ORM\OneToMany(mappedBy: 'modePaiement', targetEntity: InfoInscription::class)]
    private Collection $infoInscriptions;

    public function __construct()
    {
        $this->infoPreinscriptions = new ArrayCollection();
        $this->infoInscriptions = new ArrayCollection();
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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function isConfirmation(): ?bool
    {
        return $this->confirmation;
    }

    public function setConfirmation(bool $confirmation): static
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    /**
     * @return Collection<int, InfoPreinscription>
     */
    public function getInfoPreinscriptions(): Collection
    {
        return $this->infoPreinscriptions;
    }

    public function addInfoPreinscription(InfoPreinscription $infoPreinscription): static
    {
        if (!$this->infoPreinscriptions->contains($infoPreinscription)) {
            $this->infoPreinscriptions->add($infoPreinscription);
            $infoPreinscription->setModePaiement($this);
        }

        return $this;
    }

    public function removeInfoPreinscription(InfoPreinscription $infoPreinscription): static
    {
        if ($this->infoPreinscriptions->removeElement($infoPreinscription)) {
            // set the owning side to null (unless already changed)
            if ($infoPreinscription->getModePaiement() === $this) {
                $infoPreinscription->setModePaiement(null);
            }
        }

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
            $infoInscription->setModePaiement($this);
        }

        return $this;
    }

    public function removeInfoInscription(InfoInscription $infoInscription): static
    {
        if ($this->infoInscriptions->removeElement($infoInscription)) {
            // set the owning side to null (unless already changed)
            if ($infoInscription->getModePaiement() === $this) {
                $infoInscription->setModePaiement(null);
            }
        }

        return $this;
    }
}

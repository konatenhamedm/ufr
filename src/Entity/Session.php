<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSession = null;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Controle::class)]
    private Collection $controles;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: MoyenneMatiere::class)]
    private Collection $moyenneMatieres;

    public function __construct()
    {
        $this->controles = new ArrayCollection();
        $this->moyenneMatieres = new ArrayCollection();
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

    public function getDateSession(): ?\DateTimeInterface
    {
        return $this->dateSession;
    }

    public function setDateSession(\DateTimeInterface $dateSession): static
    {
        $this->dateSession = $dateSession;

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
            $controle->setSession($this);
        }

        return $this;
    }

    public function removeControle(Controle $controle): static
    {
        if ($this->controles->removeElement($controle)) {
            // set the owning side to null (unless already changed)
            if ($controle->getSession() === $this) {
                $controle->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MoyenneMatiere>
     */
    public function getMoyenneMatieres(): Collection
    {
        return $this->moyenneMatieres;
    }

    public function addMoyenneMatiere(MoyenneMatiere $moyenneMatiere): static
    {
        if (!$this->moyenneMatieres->contains($moyenneMatiere)) {
            $this->moyenneMatieres->add($moyenneMatiere);
            $moyenneMatiere->setSession($this);
        }

        return $this;
    }

    public function removeMoyenneMatiere(MoyenneMatiere $moyenneMatiere): static
    {
        if ($this->moyenneMatieres->removeElement($moyenneMatiere)) {
            // set the owning side to null (unless already changed)
            if ($moyenneMatiere->getSession() === $this) {
                $moyenneMatiere->setSession(null);
            }
        }

        return $this;
    }
}

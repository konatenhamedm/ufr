<?php

namespace App\Entity;

use App\Repository\ControleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ControleRepository::class)]
class Controle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'controles')]
    private ?Cours $cour = null;

    #[ORM\ManyToOne(inversedBy: 'controles')]
    private ?TypeControle $type = null;

    #[ORM\ManyToOne(inversedBy: 'controles')]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'controles')]
    private ?Semestre $semestre = null;

    #[ORM\Column(length: 255)]
    private ?string $max = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSaisie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCompo = null;

    #[ORM\OneToMany(mappedBy: 'controle', targetEntity: Note::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCour(): ?Cours
    {
        return $this->cour;
    }

    public function setCour(?Cours $cour): static
    {
        $this->cour = $cour;

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

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(?Semestre $semestre): static
    {
        $this->semestre = $semestre;

        return $this;
    }

    public function getMax(): ?string
    {
        return $this->max;
    }

    public function setMax(string $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getDateSaisie(): ?\DateTimeInterface
    {
        return $this->dateSaisie;
    }

    public function setDateSaisie(\DateTimeInterface $dateSaisie): static
    {
        $this->dateSaisie = $dateSaisie;

        return $this;
    }

    public function getDateCompo(): ?\DateTimeInterface
    {
        return $this->dateCompo;
    }

    public function setDateCompo(\DateTimeInterface $dateCompo): static
    {
        $this->dateCompo = $dateCompo;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setControle($this);
        }

        return $this;
    }



    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getControle() === $this) {
                $note->setControle(null);
            }
        }

        return $this;
    }
}

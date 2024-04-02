<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?Controle $controle = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(length: 255)]
    private ?string $note = null;

    #[ORM\Column(length: 255)]
    private ?string $MoyenneMatiere = null;

    #[ORM\OneToMany(mappedBy: 'noteEntity', targetEntity: ValeurNote::class)]
    private Collection $valeurNotes;

    public function __construct()
    {
        $this->valeurNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

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

    public function getMoyenneMatiere(): ?string
    {
        return $this->MoyenneMatiere;
    }

    public function setMoyenneMatiere(string $MoyenneMatiere): static
    {
        $this->MoyenneMatiere = $MoyenneMatiere;

        return $this;
    }

    /**
     * @return Collection<int, ValeurNote>
     */
    public function getValeurNotes(): Collection
    {
        return $this->valeurNotes;
    }

    public function addValeurNote(ValeurNote $valeurNote): static
    {
        if (!$this->valeurNotes->contains($valeurNote)) {
            $this->valeurNotes->add($valeurNote);
            $valeurNote->setNoteEntity($this);
        }

        return $this;
    }

    public function removeValeurNote(ValeurNote $valeurNote): static
    {
        if ($this->valeurNotes->removeElement($valeurNote)) {
            // set the owning side to null (unless already changed)
            if ($valeurNote->getNoteEntity() === $this) {
                $valeurNote->setNoteEntity(null);
            }
        }

        return $this;
    }
}

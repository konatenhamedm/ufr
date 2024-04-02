<?php

namespace App\Entity;

use App\Repository\ValeurNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValeurNoteRepository::class)]
class ValeurNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'valeurNotes')]
    private ?Note $noteEntity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoteEntity(): ?Note
    {
        return $this->noteEntity;
    }

    public function setNoteEntity(?Note $noteEntity): static
    {
        $this->noteEntity = $noteEntity;

        return $this;
    }
}

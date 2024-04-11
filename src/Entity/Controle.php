<?php

namespace App\Entity;

use App\Repository\ControleRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: 'cour', message: 'Impossible car ce cour existe deja')]
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
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'controles')]
    private ?Semestre $semestre = null;



    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSaisie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCompo = null;

    #[ORM\OneToMany(mappedBy: 'controle', targetEntity: Note::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $notes;

    #[ORM\OneToMany(mappedBy: 'controle', targetEntity: GroupeType::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $groupeTypes;

    /* #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null; */

    #[ORM\ManyToOne]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'controles')]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'controles')]
    private ?Classe $classe = null;

    #[ORM\ManyToOne(inversedBy: 'controles')]
    private ?AnneeScolaire $anneeScolaire = null;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->dateSaisie = new DateTime();
        $this->dateCompo = new DateTime();
        $this->groupeTypes = new ArrayCollection();
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
        //if ($note->getEtudiant() != null && $note->getMoyenneMatiere() != null) {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setControle($this);
        }
        // }

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

    /**
     * @return Collection<int, GroupeType>
     */
    public function getGroupeTypes(): Collection
    {
        return $this->groupeTypes;
    }

    public function addGroupeType(GroupeType $groupeType): static
    {
        //  if ($groupeType->getDateNote() != null && $groupeType->getType() != null && $groupeType->getCoef() != null) {

        if (!$this->groupeTypes->contains($groupeType)) {
            $this->groupeTypes->add($groupeType);
            $groupeType->setControle($this);
        }
        // }



        return $this;
    }

    public function removeGroupeType(GroupeType $groupeType): static
    {
        if ($this->groupeTypes->removeElement($groupeType)) {
            // set the owning side to null (unless already changed)
            if ($groupeType->getControle() === $this) {
                $groupeType->setControle(null);
            }
        }

        return $this;
    }


    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

        return $this;
    }

    public function getAnneeScolaire(): ?AnneeScolaire
    {
        return $this->anneeScolaire;
    }

    public function setAnneeScolaire(?AnneeScolaire $anneeScolaire): static
    {
        $this->anneeScolaire = $anneeScolaire;

        return $this;
    }
}

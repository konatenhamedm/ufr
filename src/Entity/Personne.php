<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
#[Table(name: 'user_personne')]
#[InheritanceType("JOINED")]
#[DiscriminatorColumn(name: "discr", type: "string", length: 15)]
#[DiscriminatorMap([
    'personne' => Personne::class,
    'employe' => Employe::class,
    'etudiant' => Etudiant::class
])]
//#[UniqueEntity(['email'], message: 'Cet email est déjà utilisé')]
class Personne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Veuillez renseigner le nom')]
    private ?string $nom = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Veuillez renseigner les prénoms')]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    //#[Assert\NotBlank(message: 'Veuillez renseigner la date de naissance')]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lieuNaissance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner le sexe')]
    private ?Genre $genre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner la civilité')]
    private ?Civilite $civilite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner une fonction')]
    private ?Fonction $fonction = null;

    #[ORM\OneToOne(mappedBy: 'personne', cascade: ['persist', 'remove'])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $contact = null;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: Document::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: CursusUniversitaire::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $cursusUniversitaires;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: CursusProfessionnel::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $cursusProfessionnels;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: Stage::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $stages;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->cursusUniversitaires = new ArrayCollection();
        $this->cursusProfessionnels = new ArrayCollection();
        $this->stages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(string $lieuNaissance): static
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getCivilite(): ?Civilite
    {
        return $this->civilite;
    }

    public function setCivilite(?Civilite $civilite): static
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getFonction(): ?Fonction
    {
        return $this->fonction;
    }

    public function setFonction(?Fonction $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        // set the owning side of the relation if necessary
        if ($utilisateur->getPersonne() !== $this) {
            $utilisateur->setPersonne($this);
        }

        $this->utilisateur = $utilisateur;

        return $this;
    }


    public function getNomComplet(): string
    {
        $nomComplet = $this->getNom() . ' ' . $this->getPrenom();
        return $nomComplet;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setPersonne($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getPersonne() === $this) {
                $document->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CursusUniversitaire>
     */
    public function getCursusUniversitaires(): Collection
    {
        return $this->cursusUniversitaires;
    }

    public function addCursusUniversitaire(CursusUniversitaire $cursusUniversitaire): static
    {
        if (!$this->cursusUniversitaires->contains($cursusUniversitaire)) {
            $this->cursusUniversitaires->add($cursusUniversitaire);
            $cursusUniversitaire->setPersonne($this);
        }

        return $this;
    }

    public function removeCursusUniversitaire(CursusUniversitaire $cursusUniversitaire): static
    {
        if ($this->cursusUniversitaires->removeElement($cursusUniversitaire)) {
            // set the owning side to null (unless already changed)
            if ($cursusUniversitaire->getPersonne() === $this) {
                $cursusUniversitaire->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CursusProfessionnel>
     */
    public function getCursusProfessionnels(): Collection
    {
        return $this->cursusProfessionnels;
    }

    public function addCursusProfessionnel(CursusProfessionnel $cursusProfessionnel): static
    {
        if (!$this->cursusProfessionnels->contains($cursusProfessionnel)) {
            $this->cursusProfessionnels->add($cursusProfessionnel);
            $cursusProfessionnel->setPersonne($this);
        }

        return $this;
    }

    public function removeCursusProfessionnel(CursusProfessionnel $cursusProfessionnel): static
    {
        if ($this->cursusProfessionnels->removeElement($cursusProfessionnel)) {
            // set the owning side to null (unless already changed)
            if ($cursusProfessionnel->getPersonne() === $this) {
                $cursusProfessionnel->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function addStage(Stage $stage): static
    {
        if (!$this->stages->contains($stage)) {
            $this->stages->add($stage);
            $stage->setPersonne($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): static
    {
        if ($this->stages->removeElement($stage)) {
            // set the owning side to null (unless already changed)
            if ($stage->getPersonne() === $this) {
                $stage->setPersonne(null);
            }
        }

        return $this;
    }
}

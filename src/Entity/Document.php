<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use App\Entity\Fichier;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[Table(name: 'user_document')]
/* #[Groups(['show_product'])] */
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_product', 'list_product'])]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $fichier = null;


    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;



    #[ORM\Column(length: 100, nullable: true)]
    private ?string $description = null;

    /*  #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $personne = null; */

    public function __construct()
    {
        $this->description = "document";
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le champ libelle ne peut etre null')]
    #[Groups(['show_product', 'list_product'])]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Etudiant $etudiant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /* public function getFichier(): ?Fichier
    {
        return $this->fichier;
    }

    public function setFichier(?Fichier $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    } */



    public function setFichier(?Fichier $fichier): static
    {
        $this->fichier = $fichier;
        return $this;
    }

    public function getFichier(): ?Fichier
    {
        return $this->fichier;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }


    /*   public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): static
    {
        $this->personne = $personne;

        return $this;
    } */

    /* public function getTypeDocument(): ?TypeDocument
    {
        return $this->typeDocument;
    }

    public function setTypeDocument(?TypeDocument $typeDocument): static
    {
        $this->typeDocument = $typeDocument;

        return $this;
    } */

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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
}

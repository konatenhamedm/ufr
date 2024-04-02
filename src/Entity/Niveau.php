<?php

namespace App\Entity;

use App\Repository\NiveauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: NiveauRepository::class)]
#[Table(name: 'param_niveau')]
#[UniqueConstraint(name: "code_niveau", fields: ["code", "filiere"])]
#[UniqueEntity(fields: ['code', 'filiere'], message: 'Ce code est déjà utilisé pour cette filière')]
class Niveau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un code')]
    private ?string $code = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un libellé')]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'niveaux')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner une filière')]
    private ?Filiere $filiere = null;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Frais::class, orphanRemoval: true, cascade: ['persist'])]
    #[Assert\Valid(groups: ['niveau-frais'])]
    private Collection $frais;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un responsable')]
    private ?Employe $responsable = null;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: InfoNiveau::class, cascade: ['persist'])]
    private Collection $infoNiveaux;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Inscription::class)]
    private Collection $inscriptions;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: UniteEnseignement::class)]
    private Collection $uniteEnseignements;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Classe::class)]
    private Collection $classes;




    public function __construct()
    {
        $this->frais = new ArrayCollection();
        $this->infoNiveaux = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->uniteEnseignements = new ArrayCollection();
        $this->classes = new ArrayCollection();
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

    public function getFullLibelle()
    {
        return sprintf('[%s] %s', $this->getFiliere()->getLibelle(), $this->getLibelle());
    }

    public function getFullLibelleSigle()
    {
        return sprintf('%s %s', $this->getFiliere()->getCode(), $this->getLibelle());
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

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): static
    {
        $this->filiere = $filiere;

        return $this;
    }

    /**
     * @return Collection<int, Frais>
     */
    public function getFrais(): Collection
    {
        return $this->frais;
    }

    public function addFrai(Frais $frai): static
    {
        if (!$this->frais->contains($frai)) {
            $this->frais->add($frai);
            $frai->setNiveau($this);
        }

        return $this;
    }

    public function removeFrai(Frais $frai): static
    {
        if ($this->frais->removeElement($frai)) {
            // set the owning side to null (unless already changed)
            if ($frai->getNiveau() === $this) {
                $frai->setNiveau(null);
            }
        }

        return $this;
    }

    public function getResponsable(): ?Employe
    {
        return $this->responsable;
    }

    public function setResponsable(?Employe $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * @return Collection<int, InfoNiveau>
     */
    public function getInfoNiveaux(): Collection
    {
        return $this->infoNiveaux;
    }

    public function addInfoNiveau(InfoNiveau $infoNiveau): static
    {
        if (!$this->infoNiveaux->contains($infoNiveau)) {
            $this->infoNiveaux->add($infoNiveau);
            $infoNiveau->setNiveau($this);
        }

        return $this;
    }

    public function removeInfoNiveau(InfoNiveau $infoNiveau): static
    {
        if ($this->infoNiveaux->removeElement($infoNiveau)) {
            // set the owning side to null (unless already changed)
            if ($infoNiveau->getNiveau() === $this) {
                $infoNiveau->setNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }


    public function getNom()
    {
        return $this->libelle . ' ' . $this->getFiliere()->getLibelle();
    }
    public function getSigle()
    {
        return $this->code;
    }

    /**
     * @return Collection<int, UniteEnseignement>
     */
    public function getUniteEnseignements(): Collection
    {
        return $this->uniteEnseignements;
    }

    public function addUniteEnseignement(UniteEnseignement $uniteEnseignement): static
    {
        if (!$this->uniteEnseignements->contains($uniteEnseignement)) {
            $this->uniteEnseignements->add($uniteEnseignement);
            $uniteEnseignement->setNiveau($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): static
    {
        if ($this->uniteEnseignements->removeElement($uniteEnseignement)) {
            // set the owning side to null (unless already changed)
            if ($uniteEnseignement->getNiveau() === $this) {
                $uniteEnseignement->setNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->setNiveau($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getNiveau() === $this) {
                $class->setNiveau(null);
            }
        }

        return $this;
    }
}

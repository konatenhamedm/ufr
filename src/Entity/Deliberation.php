<?php

namespace App\Entity;

use App\Repository\DeliberationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeliberationRepository::class)]
#[Table(name: 'dir_deliberation')]
class Deliberation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un examen')]
    private ?Examen $examen = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'Veuillez renseigner la date de delibération')]
    private ?\DateTimeInterface $dateExamen = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $moyenne = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: '0')]
    private ?string $total = null;

    #[ORM\OneToMany(mappedBy: 'deliberation', targetEntity: LigneDeliberation::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $ligneDeliberations;

    #[ORM\Column(length: 255)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mention $mention = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner la décision')]
    private ?string $etat = null;

    #[ORM\OneToOne(mappedBy: 'deliberation', cascade: ['persist', 'remove'])]
    #[Assert\Valid()]
    private ?DeliberationPreinscription $infoPreinscription = null;

    public function __construct()
    {
        $this->ligneDeliberations = new ArrayCollection();
        $this->setEtat('cree');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

   

    public function getExamen(): ?Examen
    {
        return $this->examen;
    }

    public function setExamen(?Examen $examen): static
    {
        $this->examen = $examen;

        return $this;
    }

    public function getDateExamen(): ?\DateTimeInterface
    {
        return $this->dateExamen;
    }

    public function setDateExamen(?\DateTimeInterface $dateExamen): static
    {
        $this->dateExamen = $dateExamen;

        return $this;
    }

    public function getMoyenne(): ?string
    {
        return $this->moyenne;
    }

    public function setMoyenne(string $moyenne): static
    {
        $this->moyenne = $moyenne;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, LigneDeliberation>
     */
    public function getLigneDeliberations(): Collection
    {
        return $this->ligneDeliberations;
    }

    public function addLigneDeliberation(LigneDeliberation $ligneDeliberation): static
    {
        if (!$this->ligneDeliberations->contains($ligneDeliberation)) {
            if ($matiereExamen = $ligneDeliberation->getMatiereExamen()) {
                $ligneDeliberation->setCoefficient($matiereExamen->getCoefficient());
            }
            $this->ligneDeliberations->add($ligneDeliberation);
            $ligneDeliberation->setDeliberation($this);
        }

        return $this;
    }

    public function removeLigneDeliberation(LigneDeliberation $ligneDeliberation): static
    {
        if ($this->ligneDeliberations->removeElement($ligneDeliberation)) {
            // set the owning side to null (unless already changed)
            if ($ligneDeliberation->getDeliberation() === $this) {
                $ligneDeliberation->setDeliberation(null);
            }
        }

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }


    public function updateTotal()
    {
        $total = 0;
        foreach ($this->getLigneDeliberations() as $ligne) {
            $total += $ligne->getNote() * $ligne->getCoefficient();
        }
        $this->setTotal($total);
        $this->updateMoyenne($total);
    }


    public function totalCoefficient()
    {
        $total = 0;
        foreach ($this->getLigneDeliberations() as $ligne) {
            $total += $ligne->getCoefficient();
        }
        return $total;
    }


    public function updateMoyenne($total)
    {
        $this->setMoyenne(round($total / $this->totalCoefficient(), 2));
    }

    public function getMention(): ?Mention
    {
        return $this->mention;
    }

    public function setMention(?Mention $mention): static
    {
        $this->mention = $mention;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getInfoPreinscription(): ?DeliberationPreinscription
    {
        return $this->infoPreinscription;
    }

    public function setInfoPreinscription(DeliberationPreinscription $infoPreinscription): static
    {
        // set the owning side of the relation if necessary
        if ($infoPreinscription->getDeliberation() !== $this) {
            $infoPreinscription->setDeliberation($this);
        }

        $this->infoPreinscription = $infoPreinscription;

        return $this;
    }


    public function getPreinscription()
    {
        return $this->getInfoPreinscription()?->getPreinscription();
    }


    public function getCandidat()
    {
        return $this->getPreinscription()?->getEtudiant();
    }
}

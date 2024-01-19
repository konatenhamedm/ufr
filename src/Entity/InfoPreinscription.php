<?php

namespace App\Entity;

use App\Repository\InfoPreinscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InfoPreinscriptionRepository::class)]
#[Table(name: 'compta_info_preinscription')]
class InfoPreinscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    #[Assert\Positive(message: 'Le montant payé doit être > à 0')]
    private ?string $montant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToOne(inversedBy: 'infoPreinscription', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Preinscription $preinscription = null;

    #[ORM\ManyToOne(inversedBy: 'infoPreinscriptions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?NaturePaiement $modePaiement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeInterface $datePaiement): static
    {
        $this->datePaiement = $datePaiement;

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

    public function getPreinscription(): ?Preinscription
    {
        return $this->preinscription;
    }

    public function setPreinscription(Preinscription $preinscription): static
    {
        $this->preinscription = $preinscription;

        return $this;
    }

    public function getModePaiement(): ?NaturePaiement
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?NaturePaiement $modePaiement): static
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
#[Table(name: 'user_etudiant')]
/* #[Groups(['show_product'])] */
class Etudiant extends Personne
{
    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: NiveauEtudiant::class)]
    private Collection $niveauEtudiants;

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    private ?Filiere $filiere = null;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Preinscription::class)]
    private Collection $preinscriptions;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Inscription::class)]
    private Collection $inscriptions;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?string $adresse = null;



    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?string $boite = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?string $fax = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $employeur = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $bailleur = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $parent = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?string $autre = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $radio = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $presse = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $affiche = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $ministere = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $mailing = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $siteWeb = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $vousMeme = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $professeur = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?bool $amiCollegue = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_product', 'list_product'])]
    private ?string $autreExistence = null;

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    #[Groups(['show_product', 'list_product'])]
    private ?Pays $pays = null;

    #[ORM\Column(length: 255)]
    #[Groups(['show_product', 'list_product'])]
    private ?string $etat = null;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Document::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: InfoEtudiant::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $infoEtudiants;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Note::class)]
    private Collection $notes;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: MoyenneMatiere::class)]
    private Collection $moyenneMatieres;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numeroWhatsapp = null;

    #[ORM\Column(length: 255)]
    private ?string $statutTravail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $travail = null;

    #[ORM\Column(length: 255)]
    private ?string $statutEtudiant = null;


    public function __construct()
    {
        $this->niveauEtudiants = new ArrayCollection();
        $this->preinscriptions = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->infoEtudiants = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->moyenneMatieres = new ArrayCollection();
        $this->statutEtudiant = 'non';
        $this->statutTravail = 'non';
    }

    /**
     * @return Collection<int, NiveauEtudiant>
     */
    public function getNiveauEtudiants(): Collection
    {
        return $this->niveauEtudiants;
    }

    public function addNiveauEtudiant(NiveauEtudiant $niveauEtudiant): static
    {
        if (!$this->niveauEtudiants->contains($niveauEtudiant)) {
            $this->niveauEtudiants->add($niveauEtudiant);
            $niveauEtudiant->setEtudiant($this);
        }

        return $this;
    }

    public function removeNiveauEtudiant(NiveauEtudiant $niveauEtudiant): static
    {
        if ($this->niveauEtudiants->removeElement($niveauEtudiant)) {
            // set the owning side to null (unless already changed)
            if ($niveauEtudiant->getEtudiant() === $this) {
                $niveauEtudiant->setEtudiant(null);
            }
        }

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
     * @return Collection<int, Preinscription>
     */
    public function getPreinscriptions(): Collection
    {
        return $this->preinscriptions;
    }

    public function addPreinscription(Preinscription $preinscription): static
    {
        if (!$this->preinscriptions->contains($preinscription)) {
            $this->preinscriptions->add($preinscription);
            $preinscription->setEtudiant($this);
        }

        return $this;
    }

    public function removePreinscription(Preinscription $preinscription): static
    {
        if ($this->preinscriptions->removeElement($preinscription)) {
            // set the owning side to null (unless already changed)
            if ($preinscription->getEtudiant() === $this) {
                $preinscription->setEtudiant(null);
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

    /*    public function addInscription(Inscription $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setEtudiant($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getEtudiant() === $this) {
                $inscription->setEtudiant(null);
            }
        }

        return $this;
    } */

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }



    public function getBoite(): ?string
    {
        return $this->boite;
    }

    public function setBoite(?string $boite): static
    {
        $this->boite = $boite;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): static
    {
        $this->fax = $fax;

        return $this;
    }

    public function isEmployeur(): ?bool
    {
        return $this->employeur;
    }

    public function setEmployeur(?bool $employeur): static
    {
        $this->employeur = $employeur;

        return $this;
    }

    public function isBailleur(): ?bool
    {
        return $this->bailleur;
    }

    public function setBailleur(?bool $bailleur): static
    {
        $this->bailleur = $bailleur;

        return $this;
    }

    public function isParent(): ?bool
    {
        return $this->parent;
    }

    public function setParent(?bool $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getAutre(): ?string
    {
        return $this->autre;
    }

    public function setAutre(?string $autre): static
    {
        $this->autre = $autre;

        return $this;
    }

    public function isRadio(): ?bool
    {
        return $this->radio;
    }

    public function setRadio(?bool $radio): static
    {
        $this->radio = $radio;

        return $this;
    }

    public function isPresse(): ?bool
    {
        return $this->presse;
    }

    public function setPresse(?bool $presse): static
    {
        $this->presse = $presse;

        return $this;
    }

    public function isAffiche(): ?bool
    {
        return $this->affiche;
    }

    public function setAffiche(?bool $affiche): static
    {
        $this->affiche = $affiche;

        return $this;
    }

    public function isMinistere(): ?bool
    {
        return $this->ministere;
    }

    public function setMinistere(?bool $ministere): static
    {
        $this->ministere = $ministere;

        return $this;
    }

    public function isMailing(): ?bool
    {
        return $this->mailing;
    }

    public function setMailing(?bool $mailing): static
    {
        $this->mailing = $mailing;

        return $this;
    }

    public function isSiteWeb(): ?bool
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(?bool $siteWeb): static
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    public function isVousMeme(): ?bool
    {
        return $this->vousMeme;
    }

    public function setVousMeme(?bool $vousMeme): static
    {
        $this->vousMeme = $vousMeme;

        return $this;
    }

    public function isProfesseur(): ?bool
    {
        return $this->professeur;
    }

    public function setProfesseur(?bool $professeur): static
    {
        $this->professeur = $professeur;

        return $this;
    }

    public function isAmiCollegue(): ?bool
    {
        return $this->amiCollegue;
    }

    public function setAmiCollegue(?bool $amiCollegue): static
    {
        $this->amiCollegue = $amiCollegue;

        return $this;
    }

    public function getAutreExistence(): ?string
    {
        return $this->autreExistence;
    }

    public function setAutreExistence(?string $autreExistence): static
    {
        $this->autreExistence = $autreExistence;

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

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
            $document->setEtudiant($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getEtudiant() === $this) {
                $document->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InfoEtudiant>
     */
    public function getInfoEtudiants(): Collection
    {
        return $this->infoEtudiants;
    }

    public function addInfoEtudiant(InfoEtudiant $infoEtudiant): static
    {
        if (!$this->infoEtudiants->contains($infoEtudiant)) {
            $this->infoEtudiants->add($infoEtudiant);
            $infoEtudiant->setEtudiant($this);
        }

        return $this;
    }

    public function removeInfoEtudiant(InfoEtudiant $infoEtudiant): static
    {
        if ($this->infoEtudiants->removeElement($infoEtudiant)) {
            // set the owning side to null (unless already changed)
            if ($infoEtudiant->getEtudiant() === $this) {
                $infoEtudiant->setEtudiant(null);
            }
        }

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
            $note->setEtudiant($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getEtudiant() === $this) {
                $note->setEtudiant(null);
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
            $moyenneMatiere->setEtudiant($this);
        }

        return $this;
    }

    public function removeMoyenneMatiere(MoyenneMatiere $moyenneMatiere): static
    {
        if ($this->moyenneMatieres->removeElement($moyenneMatiere)) {
            // set the owning side to null (unless already changed)
            if ($moyenneMatiere->getEtudiant() === $this) {
                $moyenneMatiere->setEtudiant(null);
            }
        }

        return $this;
    }

    public function getNumeroWhatsapp(): ?string
    {
        return $this->numeroWhatsapp;
    }

    public function setNumeroWhatsapp(?string $numeroWhatsapp): static
    {
        $this->numeroWhatsapp = $numeroWhatsapp;

        return $this;
    }

    public function getStatutTravail(): ?string
    {
        return $this->statutTravail;
    }

    public function setStatutTravail(string $statutTravail): static
    {
        $this->statutTravail = $statutTravail;

        return $this;
    }

    public function getTravail(): ?string
    {
        return $this->travail;
    }

    public function setTravail(?string $travail): static
    {
        $this->travail = $travail;

        return $this;
    }

    public function getStatutEtudiant(): ?string
    {
        return $this->statutEtudiant;
    }

    public function setStatutEtudiant(string $statutEtudiant): static
    {
        $this->statutEtudiant = $statutEtudiant;

        return $this;
    }
}

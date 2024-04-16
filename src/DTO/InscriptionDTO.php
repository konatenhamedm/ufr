<?php

namespace App\DTO;

use App\Entity\Civilite;
use App\Entity\Filiere;
use App\Entity\Genre;
use App\Entity\Niveau;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

class InscriptionDTO
{
    #[Assert\NotBlank(message: 'Veuillez renseigner votre nom')]
    private ?string $nom;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre genre')]
    private ?Genre $genre;

    /* #[Assert\NotBlank(message: 'Veuillez renseigner votre civilité')]
    private ?Civilite $civilite; */

    #[Assert\NotBlank(message: 'Veuillez sélectionner un niveau')]
    private ?Niveau $niveau;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre prénom')]
    private ?string $prenom;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre date de naissance')]
    private ?DateTimeInterface $dateNaissance;


    private ?string $username;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre mot de passe')]
    private ?string $plainPassword;

    private ?string $contact;

    #[Assert\NotBlank(message: 'Veuillez renseigner email')]
    private ?string $email = null;


    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of nom
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of nom
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     */
    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of prenom
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * Set the value of prenom
     */
    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get the value of dateNaissance
     */
    public function getDateNaissance(): ?DateTimeInterface
    {
        return $this->dateNaissance;
    }

    /**
     * Set the value of dateNaissance
     */
    public function setDateNaissance(?DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get the value of username
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set the value of username
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of plainPassword
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }




    /**
     * Get the value of genre
     */
    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    /**
     * Set the value of genre
     */
    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get the value of contact
     */
    public function getContact(): ?string
    {
        return $this->contact;
    }

    /**
     * Set the value of contact
     */
    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }



    /**
     * Get the value of filiere
     */
    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    /**
     * Set the value of filiere
     */
    public function setNiveau(?Niveau $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }
}

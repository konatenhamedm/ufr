<?php

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

class GroupeDTO
{
    #[Assert\NotBlank(message: 'Veuillez rensigner le libellé')]
    private string $libelle;

    private ?string $description;

    /**
     * Undocumented variable
     *
     * @var ArrayCollection
     */
    private $modules;


    public function __construct()
    {
        $this->modules = new ArrayCollection();
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * Set the value of name
     *
     * @param  string  $libelle
     *
     * @return  self
     */ 
    public function setLibelle(?string $libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

     /**
     * @return Collection|GroupeModuleDTO[]
     */
    public function getModules()
    {
        return $this->modules;
    }

    public function addModule(GroupeModuleDTO $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->setGroupe($this);
        }
        return $this;
    }


    public function removeModule(GroupeModuleDTO $module): self
    {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getGroupe() === $this) {
                $module->setGroupe(null);
            }
        }

        return $this;
    }


    public function getRoles(): array
    {
        $roles = [];

        foreach ($this->modules as $module) {
            foreach ($module->getRoles() as $role) {
                $roles[] = $role;
            }
        }
        return $roles;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
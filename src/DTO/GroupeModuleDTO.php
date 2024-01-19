<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GroupeModuleDTO
{
  
    private string $name;

    private string  $child;

    private string $module;

    private array $roles;

    private array $listRoles;
    
    private  ?GroupeDTO $groupe;

    /**
     * Get undocumented variable
     *
     * @return  array
     */ 
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set undocumented variable
     *
     * @param  array  $roles  Undocumented variable
     *
     * @return  self
     */ 
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get undocumented variable
     *
     * @return  GroupeDTO
     */ 
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Set undocumented variable
     *
     * @param  GroupeDTO  $groupe  Undocumented variable
     *
     * @return  self
     */ 
    public function setGroupe(?GroupeDTO $groupe)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get the value of module
     *
     * @return  string
     */ 
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set the value of module
     *
     * @param  string  $module
     *
     * @return  self
     */ 
    public function setModule(string $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get undocumented variable
     *
     * @return  array
     */ 
    public function getListRoles()
    {
        return $this->listRoles;
    }

    /**
     * Set undocumented variable
     *
     * @param  array  $listRoles  Undocumented variable
     *
     * @return  self
     */ 
    public function setListRoles(array $listRoles)
    {
        $this->listRoles = $listRoles;

        return $this;
    }

    /**
     * Get the value of child
     *
     * @return  string
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set the value of child
     *
     * @param  string  $child
     *
     * @return  self
     */
    public function setChild(string $child)
    {
        $this->child = $child;

        return $this;
    }
}
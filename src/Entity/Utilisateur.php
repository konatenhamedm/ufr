<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
//#[UniqueEntity(['email'], message: 'Ce pseudo est déjà utilisé')]
#[UniqueEntity(fields: 'email', message: 'Ce pseudo est déjà utilisé')]
#[ORM\Table(name: 'user_utilisateur')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180,nullable: true)]
    private ?string $username = null;


    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(type: 'string',nullable: true)]
    private ?string $resetToken;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $password = null;


    #[Assert\NotBlank(message: 'Veuillez renseigner le mot de passe', groups: ['Registration'])]
    private ?string $plainPassword = null;

    #[ORM\OneToOne(inversedBy: 'utilisateur', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $personne = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: UtilisateurGroupe::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $utilisateurGroupes;

    private Collection $groupes;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: ' renseigner le mail')]
    private ?string $email = null;

    public function __construct()
    {
        $this->utilisateurGroupes = new ArrayCollection();
        $this->groupes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = (array)$this->roles;
        $roles[] = 'ROLE_USER';
        foreach ($this->getGroupes() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * {@inheritDoc}
     */
    public function isEqualTo(UserInterface $user): bool
    {
        return $this->getUsername() == $user->getUserIdentifier();
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


    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(Personne $personne): static
    {
        $this->personne = $personne;

        return $this;
    }

    /**
     * @return Collection<int, UtilisateurGroupe>
     */
    public function getUtilisateurGroupes(): Collection
    {
        return $this->utilisateurGroupes;
    }

    public function addUtilisateurGroupe(UtilisateurGroupe $utilisateurGroupe): static
    {
        if (!$this->utilisateurGroupes->contains($utilisateurGroupe)) {
            $this->utilisateurGroupes->add($utilisateurGroupe);
            $utilisateurGroupe->setUtilisateur($this);
        }

        return $this;
    }

    public function removeUtilisateurGroupe(UtilisateurGroupe $utilisateurGroupe): static
    {
        if ($this->utilisateurGroupes->removeElement($utilisateurGroupe)) {
            // set the owning side to null (unless already changed)
            if ($utilisateurGroupe->getUtilisateur() === $this) {
                $utilisateurGroupe->setUtilisateur(null);
            }
        }

        return $this;
    }


    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupes(): Collection
    {
        return $this->utilisateurGroupes->count() ?
            $this->utilisateurGroupes->map(fn (UtilisateurGroupe $utilisateurGroupe) => $utilisateurGroupe->getGroupe()) :
            $this->groupes = new ArrayCollection();
    }


    public function getNomComplet(): string
    {
        return $this->getPersonne()->getNomComplet();
    }

    public function hasGroupe(Groupe $groupe)
    {
        return $this->utilisateurGroupes
            ->filter(fn (UtilisateurGroupe $utilisateurGroupe) => $groupe == $utilisateurGroupe->getGroupe())
            ->current();
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->hasGroupe($groupe)) {
            $utilisateurGroupe = new UtilisateurGroupe();
            $utilisateurGroupe->setGroupe($groupe);
            $this->groupes->add($groupe);
            $this->addUtilisateurGroupe($utilisateurGroupe);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->removeElement($groupe) && ($utilisateurGroupe = $this->hasGroupe($groupe))) {
            $this->removeUtilisateurGroupe($utilisateurGroupe);
        }

        return $this;
    }


    public function hasRoleOnModuleChild($module, $child)
    {
        $module = strtoupper($module);
        $child = strtoupper($child);
        $result = false;
        foreach ($this->getRoles() as $role) {
            if (preg_match("/^ROLE_([A-Z_]+)_{$module}_([A-Z_]+)_{$child}/", $role)) {
                $result = true;
                break;
            }
        }
        return $this->hasRole('ROLE_ADMIN') || $result;
    }


    public function hasRoleOnAlias($module, $alias, $roleName)
    {
        $roleAlias = strtoupper(strtr($alias, '.', '_'));
        $role = "{$roleName}_{$module}_{$roleAlias}";

        return $this->hasRole('ROLE_ADMIN') || $this->hasRole($role);
    }


    public function hasRoleNameOnModuleChild($roleName, $module, $child)
    {
        $module = strtoupper($module);
        $child = strtoupper($child);
        $roleName = strtoupper($roleName);
        $result = false;

        foreach ($this->getRoles() as $role) {
            $regex = "^ROLE_{$roleName}_{$module}_([A-Z_]+)";
            if ($child) {
                $regex .= "_{$child}";
            }

            if (preg_match("/{$regex}/", $role)) {
                $result = true;
                break;
            }
        }
        return $this->hasRole('ROLE_ADMIN') || $result;
    }


    public function hasRoleOnModuleController($module,  $controller)
    {
        $module = strtoupper($module);
        $controller = strtoupper($controller);
        $result = false;
        foreach ($this->getRoles() as $role) {
            if (preg_match("/^ROLE_([A-Z_]+)_{$module}_{$controller}/", $role)) {
                $result = true;
                break;
            }
        }
        return $this->hasRole('ROLE_ADMIN') || $result;
    }


    public function hasRoleOnModuleControllers($module,  array $controllers)
    {
        $module = strtoupper($module);
        $controllers = array_map(function ($controller) {
            return strtoupper($controller);
        }, $controllers);
        $lsControllers = implode('|', $controllers);
        $result = false;
        foreach ($this->getRoles() as $role) {
            if (preg_match("/^ROLE_([A-Z_]+)_{$module}_({$lsControllers})/", $role)) {
                $result = true;
                break;
            }
        }
        return $this->hasRole('ROLE_ADMIN') || $result;
    }



    public function hasAllRoleOnModule($roleName, $module, $controller, $child = null, $as = null)
    {
        $module = strtoupper($module);

        $roleName = strtoupper($roleName);
        $controller = $as ? strtoupper($as) : strtoupper($controller);
        $result = false;




        foreach ($this->getRoles() as $role) {
            $regex = "^ROLE_{$roleName}_{$module}_{$controller}";

            if ($child) {
                $regex .= strtoupper("_{$child}");
            }

            if (preg_match("/{$regex}$/", $role)) {
                $result = true;
                break;
            }
        }
        return $this->hasRole('ROLE_ADMIN') || $result;
    }



    public function hasRoleStartsWith($roleName)
    {
        $result = false;

        foreach ($this->getRoles() as $role) {
            if (preg_match("/^{$roleName}/", $role, $matches)) {
                $result = true;
                break;
            }
        }
        return $this->hasRole('ROLE_ADMIN') || $result;
    }

    public function hasRoleOnModule(string $module, $exclude = null, $append = null)
    {
        $module = strtoupper($module);
        $result = false;

        $exclude = (array)$exclude;


        foreach ($this->getRoles() as $role) {
            $regex = "/^ROLE_{$module}_([A-Z_]+)";


            if ($append) {
                $regex .= strtoupper($append);
            }
            $regex .= "/";



            if (preg_match($regex, $role, $matches)) {

                $lowerMatch = strtolower($matches[1]);

                if (!$exclude || ($exclude &&  !in_array($lowerMatch, $exclude))) {
                    $result = true;
                    break;
                }
            }
        }
        return $this->hasRole('ROLE_ADMIN') || $result;
    }


    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === 'ROLE_USER') {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }



    /**
     * @param $roles
     */
    public function hasRoles($roles)
    {
        return array_intersect($this->getRoles(), $roles);
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

    /**
     * Get the value of resetToken
     */
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    /**
     * Set the value of resetToken
     *
     * @return  self
     */
    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }
}

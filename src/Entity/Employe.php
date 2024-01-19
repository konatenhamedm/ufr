<?php

namespace App\Entity;

use App\Repository\EmployeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
#[Table(name: 'user_employe')]
class Employe extends Personne
{
   
}

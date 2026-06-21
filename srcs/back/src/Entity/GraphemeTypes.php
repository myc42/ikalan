<?php

namespace App\Entity;

use App\Repository\GraphemeTypesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GraphemeTypesRepository::class)]
class GraphemeTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
{
    // Remplace 'name' ou 'title' par la propriété qui a du sens pour l'entité
    return $this->name ?? 'Élément sans nom'; 
}
}

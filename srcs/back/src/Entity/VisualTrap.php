<?php

namespace App\Entity;

use App\Repository\VisualTrapRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisualTrapRepository::class)]
class VisualTrap
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\Column(type: 'text[]')]
    private array $list = [];

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

    public function getList(): array
    {
        return $this->list;
    }

    public function setList(array $list): static
    {
        $this->list = $list;

        return $this;
    }

    public function __toString(): string
{
    // Remplace 'name' ou 'title' par la propriété qui a du sens pour l'entité
    return $this->name ?? 'Élément sans nom'; 
}


}

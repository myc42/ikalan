<?php

namespace App\Entity;

use App\Repository\AudioFilesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AudioFilesRepository::class)]
class AudioFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 90)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $storage_key = null;

    #[ORM\Column]
    private ?int $duration_ms = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStorageKey(): ?string
    {
        return $this->storage_key;
    }

    public function setStorageKey(string $storage_key): static
    {
        $this->storage_key = $storage_key;

        return $this;
    }

    public function getDurationMs(): ?int
    {
        return $this->duration_ms;
    }

    public function setDurationMs(int $duration_ms): static
    {
        $this->duration_ms = $duration_ms;

        return $this;
    }

    public function __toString(): string
{
    // Retourne la propriété qui représente le mieux l'entité (ex: le nom)
    return $this->name ?? 'Audio sans nom'; 
}
}

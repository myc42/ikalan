<?php

namespace App\Entity;

use App\Repository\ModuleVerbsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleVerbsRepository::class)]
class ModuleVerbs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'moduleVerbs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modules $moduleId = null;

    #[ORM\ManyToOne(inversedBy: 'moduleVerbs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Verbs $verbId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuleId(): ?Modules
    {
        return $this->moduleId;
    }

    public function setModuleId(?Modules $moduleId): static
    {
        $this->moduleId = $moduleId;

        return $this;
    }

    public function getVerbId(): ?Verbs
    {
        return $this->verbId;
    }

    public function setVerbId(?Verbs $verbId): static
    {
        $this->verbId = $verbId;

        return $this;
    }
}

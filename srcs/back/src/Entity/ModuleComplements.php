<?php

namespace App\Entity;

use App\Repository\ModuleComplementsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleComplementsRepository::class)]
class ModuleComplements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'moduleComplements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modules $moduleId = null;

    #[ORM\ManyToOne(inversedBy: 'moduleComplements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Complements $complementId = null;

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

    public function getComplementId(): ?Complements
    {
        return $this->complementId;
    }

    public function setComplementId(?Complements $complementId): static
    {
        $this->complementId = $complementId;

        return $this;
    }
}

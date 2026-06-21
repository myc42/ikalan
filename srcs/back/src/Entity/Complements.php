<?php

namespace App\Entity;

use App\Repository\ComplementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplementsRepository::class)]
class Complements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConstraintTags $constraintTagId = null;

    #[ORM\Column(type: 'text[]')]
    private array $phoneticList = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AudioFiles $audioPathId = null;

    /**
     * @var Collection<int, ModuleComplements>
     */
    #[ORM\OneToMany(targetEntity: ModuleComplements::class, mappedBy: 'complementId')]
    private Collection $moduleComplements;

    public function __construct()
    {
        $this->moduleComplements = new ArrayCollection();
    }

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

    public function getConstraintTagId(): ?ConstraintTags
    {
        return $this->constraintTagId;
    }

    public function setConstraintTagId(?ConstraintTags $constraintTagId): static
    {
        $this->constraintTagId = $constraintTagId;

        return $this;
    }

    public function getPhoneticList(): array
    {
        return $this->phoneticList;
    }

    public function setPhoneticList(array $phoneticList): static
    {
        $this->phoneticList = $phoneticList;

        return $this;
    }

    public function getAudioPathId(): ?AudioFiles
    {
        return $this->audioPathId;
    }

    public function setAudioPathId(?AudioFiles $audioPathId): static
    {
        $this->audioPathId = $audioPathId;

        return $this;
    }

    /**
     * @return Collection<int, ModuleComplements>
     */
    public function getModuleComplements(): Collection
    {
        return $this->moduleComplements;
    }

    public function addModuleComplement(ModuleComplements $moduleComplement): static
    {
        if (!$this->moduleComplements->contains($moduleComplement)) {
            $this->moduleComplements->add($moduleComplement);
            $moduleComplement->setComplementId($this);
        }

        return $this;
    }

    public function removeModuleComplement(ModuleComplements $moduleComplement): static
    {
        if ($this->moduleComplements->removeElement($moduleComplement)) {
            // set the owning side to null (unless already changed)
            if ($moduleComplement->getComplementId() === $this) {
                $moduleComplement->setComplementId(null);
            }
        }

        return $this;
    }

     public function __toString(): string
    {
        return $this->name ?? 'Niveau sans nom';
    }
}

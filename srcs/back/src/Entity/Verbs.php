<?php

namespace App\Entity;

use App\Repository\VerbsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VerbsRepository::class)]
class Verbs
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

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonNumbers $personId = null;

    #[ORM\Column(type: 'text[]')]
    private array $phoneticList = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AudioFiles $audioPathId = null;

    /**
     * @var Collection<int, ModuleVerbs>
     */
    #[ORM\OneToMany(targetEntity: ModuleVerbs::class, mappedBy: 'verbId')]
    private Collection $moduleVerbs;

    public function __construct()
    {
        $this->moduleVerbs = new ArrayCollection();
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

    public function getPersonId(): ?PersonNumbers
    {
        return $this->personId;
    }

    public function setPersonId(?PersonNumbers $personId): static
    {
        $this->personId = $personId;

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
     * @return Collection<int, ModuleVerbs>
     */
    public function getModuleVerbs(): Collection
    {
        return $this->moduleVerbs;
    }

    public function addModuleVerb(ModuleVerbs $moduleVerb): static
    {
        if (!$this->moduleVerbs->contains($moduleVerb)) {
            $this->moduleVerbs->add($moduleVerb);
            $moduleVerb->setVerbId($this);
        }

        return $this;
    }

    public function removeModuleVerb(ModuleVerbs $moduleVerb): static
    {
        if ($this->moduleVerbs->removeElement($moduleVerb)) {
            // set the owning side to null (unless already changed)
            if ($moduleVerb->getVerbId() === $this) {
                $moduleVerb->setVerbId(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Verbe sans nom';
    }
}

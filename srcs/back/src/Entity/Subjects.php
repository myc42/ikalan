<?php

namespace App\Entity;

use App\Repository\SubjectsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectsRepository::class)]
class Subjects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonNumbers $personId = null;

    #[ORM\Column(type: 'text[]')]
    private array $phoneticList = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AudioFiles $audioPathId = null;

    /**
     * @var Collection<int, ModuleSubjects>
     */
    #[ORM\OneToMany(targetEntity: ModuleSubjects::class, mappedBy: 'subjectId')]
    private Collection $moduleSubjects;

    public function __construct()
    {
        $this->moduleSubjects = new ArrayCollection();
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
     * @return Collection<int, ModuleSubjects>
     */
    public function getModuleSubjects(): Collection
    {
        return $this->moduleSubjects;
    }

    public function addModuleSubject(ModuleSubjects $moduleSubject): static
    {
        if (!$this->moduleSubjects->contains($moduleSubject)) {
            $this->moduleSubjects->add($moduleSubject);
            $moduleSubject->setSubjectId($this);
        }

        return $this;
    }

    public function removeModuleSubject(ModuleSubjects $moduleSubject): static
    {
        if ($this->moduleSubjects->removeElement($moduleSubject)) {
            // set the owning side to null (unless already changed)
            if ($moduleSubject->getSubjectId() === $this) {
                $moduleSubject->setSubjectId(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        // Remplace 'name' ou 'title' par la propriété texte qui définit ton Sujet
        return $this->name ?? 'Sujet sans nom'; 
    }
}

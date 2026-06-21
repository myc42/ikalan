<?php

namespace App\Entity;

use App\Repository\ModulesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModulesRepository::class)]
class Modules
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chapters $chapterId = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $module_order = null;

    #[ORM\Column(type: 'text[]')]
    private array $description = [];

    #[ORM\Column]
    private ?int $content_version = null;

    /**
     * @var Collection<int, ModuleSubjects>
     */
    #[ORM\OneToMany(targetEntity: ModuleSubjects::class, mappedBy: 'moduleId')]
    private Collection $moduleSubjects;

    /**
     * @var Collection<int, ModuleVerbs>
     */
    #[ORM\OneToMany(targetEntity: ModuleVerbs::class, mappedBy: 'moduleId')]
    private Collection $moduleVerbs;

    /**
     * @var Collection<int, ModuleComplements>
     */
    #[ORM\OneToMany(targetEntity: ModuleComplements::class, mappedBy: 'moduleId')]
    private Collection $moduleComplements;

    public function __construct()
    {
        $this->moduleSubjects = new ArrayCollection();
        $this->moduleVerbs = new ArrayCollection();
        $this->moduleComplements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChapterId(): ?Chapters
    {
        return $this->chapterId;
    }

    public function setChapterId(?Chapters $chapterId): static
    {
        $this->chapterId = $chapterId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getModuleOrder(): ?int
    {
        return $this->module_order;
    }

    public function setModuleOrder(int $module_order): static
    {
        $this->module_order = $module_order;

        return $this;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function setDescription(array $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getContentVersion(): ?int
    {
        return $this->content_version;
    }

    public function setContentVersion(int $content_version): static
    {
        $this->content_version = $content_version;

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
            $moduleSubject->setModuleId($this);
        }

        return $this;
    }

    public function removeModuleSubject(ModuleSubjects $moduleSubject): static
    {
        if ($this->moduleSubjects->removeElement($moduleSubject)) {
            // set the owning side to null (unless already changed)
            if ($moduleSubject->getModuleId() === $this) {
                $moduleSubject->setModuleId(null);
            }
        }

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
            $moduleVerb->setModuleId($this);
        }

        return $this;
    }

    public function removeModuleVerb(ModuleVerbs $moduleVerb): static
    {
        if ($this->moduleVerbs->removeElement($moduleVerb)) {
            // set the owning side to null (unless already changed)
            if ($moduleVerb->getModuleId() === $this) {
                $moduleVerb->setModuleId(null);
            }
        }

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
            $moduleComplement->setModuleId($this);
        }

        return $this;
    }

    public function removeModuleComplement(ModuleComplements $moduleComplement): static
    {
        if ($this->moduleComplements->removeElement($moduleComplement)) {
            // set the owning side to null (unless already changed)
            if ($moduleComplement->getModuleId() === $this) {
                $moduleComplement->setModuleId(null);
            }
        }

        return $this;
    }

 public function __toString(): string
    {
        return $this->title ?? 'Module sans titre'; // Adapte selon la propriété de ton entité Module
    }
}

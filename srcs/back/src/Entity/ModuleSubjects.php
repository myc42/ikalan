<?php

namespace App\Entity;

use App\Repository\ModuleSubjectsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleSubjectsRepository::class)]
class ModuleSubjects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'moduleSubjects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modules $moduleId = null;

    #[ORM\ManyToOne(inversedBy: 'moduleSubjects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subjects $subjectId = null;

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

    public function getSubjectId(): ?Subjects
    {
        return $this->subjectId;
    }

    public function setSubjectId(?Subjects $subjectId): static
    {
        $this->subjectId = $subjectId;

        return $this;
    }
}

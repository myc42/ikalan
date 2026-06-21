<?php

namespace App\Entity;

use App\Repository\GraphemesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GraphemesRepository::class)]
class Graphemes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modules $moduleId = null;

    #[ORM\Column(length: 5)]
    private ?string $min = null;

    #[ORM\Column(length: 5)]
    private ?string $maj = null;

    #[ORM\Column(length: 50)]
    private ?string $word = null;

    #[ORM\Column]
    private ?bool $isSilentLetter = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AudioFiles $audioPath = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?VisualTrap $visualTrap = null;

    #[ORM\Column(type: 'text[]')]
    private array $phoneticList = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GraphemeTypes $typeId = null;

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

    public function getMin(): ?string
    {
        return $this->min;
    }

    public function setMin(string $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMaj(): ?string
    {
        return $this->maj;
    }

    public function setMaj(string $maj): static
    {
        $this->maj = $maj;

        return $this;
    }

    public function getWord(): ?string
    {
        return $this->word;
    }

    public function setWord(string $word): static
    {
        $this->word = $word;

        return $this;
    }

    public function isSilentLetter(): ?bool
    {
        return $this->isSilentLetter;
    }

    public function setIsSilentLetter(bool $isSilentLetter): static
    {
        $this->isSilentLetter = $isSilentLetter;

        return $this;
    }

    public function getAudioPath(): ?AudioFiles
    {
        return $this->audioPath;
    }

    public function setAudioPath(?AudioFiles $audioPath): static
    {
        $this->audioPath = $audioPath;

        return $this;
    }

    public function getVisualTrap(): ?VisualTrap
    {
        return $this->visualTrap;
    }

    public function setVisualTrap(?VisualTrap $visualTrap): static
    {
        $this->visualTrap = $visualTrap;

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

    public function getTypeId(): ?GraphemeTypes
    {
        return $this->typeId;
    }

    public function setTypeId(?GraphemeTypes $typeId): static
    {
        $this->typeId = $typeId;

        return $this;
    }
}

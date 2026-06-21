<?php

namespace App\Entity;

use App\Repository\WordsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WordsRepository::class)]
class Words
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $word = null;

    #[ORM\Column(type: 'text[]')]
    private array $segmentation = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AudioFiles $audioPath = null;

    #[ORM\Column(type: 'text[]')]
    private array $phoneticList = [];

    #[ORM\ManyToOne(inversedBy: 'words')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Levels $levelId = null;

    #[ORM\Column]
    private ?bool $isSyllable = null;

    #[ORM\Column]
    private ?bool $isSightWord = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSegmentation(): array
    {
        return $this->segmentation;
    }

    public function setSegmentation(array $segmentation): static
    {
        $this->segmentation = $segmentation;

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

    public function getPhoneticList(): array
    {
        return $this->phoneticList;
    }

    public function setPhoneticList(array $phoneticList): static
    {
        $this->phoneticList = $phoneticList;

        return $this;
    }

    public function getLevelId(): ?Levels
    {
        return $this->levelId;
    }

    public function setLevelId(?Levels $levelId): static
    {
        $this->levelId = $levelId;

        return $this;
    }

    public function isSyllable(): ?bool
    {
        return $this->isSyllable;
    }

    public function setIsSyllable(bool $isSyllable): static
    {
        $this->isSyllable = $isSyllable;

        return $this;
    }

    public function isSightWord(): ?bool
    {
        return $this->isSightWord;
    }

    public function setIsSightWord(bool $isSightWord): static
    {
        $this->isSightWord = $isSightWord;

        return $this;
    }

    public function __toString(): string
    {
        return $this->word ?? 'Mot sans nom';
    }
}

<?php

namespace App\Entity;

use App\Repository\LevelsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LevelsRepository::class)]
class Levels
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, Words>
     */
    #[ORM\OneToMany(targetEntity: Words::class, mappedBy: 'levelId')]
    private Collection $words;

    public function __construct()
    {
        $this->words = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Words>
     */
    public function getWords(): Collection
    {
        return $this->words;
    }

    public function addWord(Words $word): static
    {
        if (!$this->words->contains($word)) {
            $this->words->add($word);
            $word->setLevelId($this);
        }

        return $this;
    }

    public function removeWord(Words $word): static
    {
        if ($this->words->removeElement($word)) {
            // set the owning side to null (unless already changed)
            if ($word->getLevelId() === $this) {
                $word->setLevelId(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Niveau sans nom';
    }
}

<?php

namespace App\Entity;

use App\Repository\UserStreaksRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserStreaksRepository::class)]
class UserStreaks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // ✅ La bonne propriété avec inversedBy pour que le User puisse la sauvegarder !
    #[ORM\OneToOne(inversedBy: 'userStreaks', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $longestStreak = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastActivityAt = null;

    public function __construct()
    {
        $this->lastActivityAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // ✅ Uniquement les bonnes méthodes pour $user (les anciennes avec UserId sont effacées)
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getLongestStreak(): ?int
    {
        return $this->longestStreak;
    }

    public function setLongestStreak(int $longestStreak): static
    {
        $this->longestStreak = $longestStreak;

        return $this;
    }

    public function getLastActivityAt(): ?\DateTimeImmutable
    {
        return $this->lastActivityAt;
    }

    public function setLastActivityAt(\DateTimeImmutable $lastActivityAt): static
    {
        $this->lastActivityAt = $lastActivityAt;

        return $this;
    }
}
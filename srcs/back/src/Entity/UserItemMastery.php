<?php

namespace App\Entity;

use App\Repository\UserItemMasteryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserItemMasteryRepository::class)]
class UserItemMastery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\Column(length: 50)]
    private ?string $itemType = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $itemId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 3)]
    private ?string $masteryScore = null;

    #[ORM\Column]
    private ?int $avgResponseMs = null;

    #[ORM\Column]
    private ?int $errorCount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastSeenAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getItemType(): ?string
    {
        return $this->itemType;
    }

    public function setItemType(string $itemType): static
    {
        $this->itemType = $itemType;

        return $this;
    }

    public function getItemId(): ?string
    {
        return $this->itemId;
    }

    public function setItemId(string $itemId): static
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function getMasteryScore(): ?string
    {
        return $this->masteryScore;
    }

    public function setMasteryScore(string $masteryScore): static
    {
        $this->masteryScore = $masteryScore;

        return $this;
    }

    public function getAvgResponseMs(): ?int
    {
        return $this->avgResponseMs;
    }

    public function setAvgResponseMs(int $avgResponseMs): static
    {
        $this->avgResponseMs = $avgResponseMs;

        return $this;
    }

    public function getErrorCount(): ?int
    {
        return $this->errorCount;
    }

    public function setErrorCount(int $errorCount): static
    {
        $this->errorCount = $errorCount;

        return $this;
    }

    public function getLastSeenAt(): ?\DateTimeImmutable
    {
        return $this->lastSeenAt;
    }

    public function setLastSeenAt(\DateTimeImmutable $lastSeenAt): static
    {
        $this->lastSeenAt = $lastSeenAt;

        return $this;
    }
}

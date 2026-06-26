<?php

namespace App\Entity;

use App\Repository\UserModuleProgressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


enum UserModuleStatus: string
{
    case REVIEW = 'review';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}

#[ORM\Entity(repositoryClass: UserModuleProgressRepository::class)]
class UserModuleProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userModuleProgress')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'userModuleProgress')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modules $moduleId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 3)]
    private ?string $globalScore = null;

    #[ORM\Column]
    private ?int $consecutivePerfectScores = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 3)]
    private ?string $easeFactor = null;

    #[ORM\Column]
    private ?int $intervalDays = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $targetAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $windowStartAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $windowEndAt = null;

    #[ORM\Column(enumType: UserModuleStatus::class)]
    private ?UserModuleStatus $status = null;

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

    public function getModuleId(): ?Modules
    {
        return $this->moduleId;
    }

    public function setModuleId(?Modules $moduleId): static
    {
        $this->moduleId = $moduleId;

        return $this;
    }

    public function getGlobalScore(): ?string
    {
        return $this->globalScore;
    }

    public function setGlobalScore(string $globalScore): static
    {
        $this->globalScore = $globalScore;

        return $this;
    }

    public function getConsecutivePerfectScores(): ?int
    {
        return $this->consecutivePerfectScores;
    }

    public function setConsecutivePerfectScores(int $consecutivePerfectScores): static
    {
        $this->consecutivePerfectScores = $consecutivePerfectScores;

        return $this;
    }

    public function getEaseFactor(): ?string
    {
        return $this->easeFactor;
    }

    public function setEaseFactor(string $easeFactor): static
    {
        $this->easeFactor = $easeFactor;

        return $this;
    }

    public function getIntervalDays(): ?int
    {
        return $this->intervalDays;
    }

    public function setIntervalDays(int $intervalDays): static
    {
        $this->intervalDays = $intervalDays;

        return $this;
    }

    public function getTargetAt(): ?\DateTimeImmutable
    {
        return $this->targetAt;
    }

    public function setTargetAt(\DateTimeImmutable $targetAt): static
    {
        $this->targetAt = $targetAt;

        return $this;
    }

    public function getWindowStartAt(): ?\DateTimeImmutable
    {
        return $this->windowStartAt;
    }

    public function setWindowStartAt(\DateTimeImmutable $windowStartAt): static
    {
        $this->windowStartAt = $windowStartAt;

        return $this;
    }

    public function getWindowEndAt(): ?\DateTimeImmutable
    {
        return $this->windowEndAt;
    }

    public function setWindowEndAt(\DateTimeImmutable $windowEndAt): static
    {
        $this->windowEndAt = $windowEndAt;

        return $this;
    }

    public function getStatus(): ?UserModuleStatus
{
    return $this->status;
}

public function setStatus(?UserModuleStatus $status): static
{
    $this->status = $status;

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

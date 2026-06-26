<?php

namespace App\Entity;

use App\Repository\SessionLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionLogRepository::class)]
class SessionLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modules $moduleId = null;

    #[ORM\Column]
    private array $rawEvents = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $receivedAt = null;

  
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $processedAt = null;

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

    public function getRawEvents(): array
    {
        return $this->rawEvents;
    }

    public function setRawEvents(array $rawEvents): static
    {
        $this->rawEvents = $rawEvents;

        return $this;
    }

    public function getReceivedAt(): ?\DateTimeImmutable
    {
        return $this->receivedAt;
    }

    public function setReceivedAt(\DateTimeImmutable $receivedAt): static
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeImmutable $processedAt): static
    {
        $this->processedAt = $processedAt;

        return $this;
    }
}

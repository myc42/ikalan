<?php

namespace App\Entity;

use App\Repository\TrophyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrophyRepository::class)]
class Trophy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\Column]
    private ?int $perfectChapter = null;

    #[ORM\Column]
    private ?int $moduleMaster = null;

    #[ORM\Column]
    private ?int $flawlessStreak = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getPerfectChapter(): ?int
    {
        return $this->perfectChapter;
    }

    public function setPerfectChapter(int $perfectChapter): static
    {
        $this->perfectChapter = $perfectChapter;

        return $this;
    }

    public function getModuleMaster(): ?int
    {
        return $this->moduleMaster;
    }

    public function setModuleMaster(int $moduleMaster): static
    {
        $this->moduleMaster = $moduleMaster;

        return $this;
    }

    public function getFlawlessStreak(): ?int
    {
        return $this->flawlessStreak;
    }

    public function setFlawlessStreak(int $flawlessStreak): static
    {
        $this->flawlessStreak = $flawlessStreak;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }
}

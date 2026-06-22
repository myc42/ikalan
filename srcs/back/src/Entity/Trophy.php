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

    // ✅ Ajout de inversedBy: 'trophy' pour faire le lien avec l'entité User
    #[ORM\OneToOne(inversedBy: 'trophy', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $perfectChapter = 0;

    #[ORM\Column]
    private ?int $moduleMaster = 0;

    #[ORM\Column]
    private ?int $flawlessStreak = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    public function __construct()
    {
        $this->updateAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // ✅ Les bonnes méthodes pour manipuler $user (les anciennes sont supprimées)
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
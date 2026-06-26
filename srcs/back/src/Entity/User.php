<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Repository\UserStreaksRepository;
use App\Repository\TrophyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;





use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_TELEPHONE', fields: ['telephone'])]
#[ORM\HasLifecycleCallbacks]  // ← ajouter ça
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $telephone = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $birthdayAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $registerAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Trophy::class, cascade: ['persist', 'remove'])]
    private ?Trophy $trophy = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: UserStreaks::class, cascade: ['persist', 'remove'])]
    private ?UserStreaks $userStreaks = null;

    /**
     * @var Collection<int, Progression>
     */
    #[ORM\OneToMany(targetEntity: Progression::class, mappedBy: 'userId', orphanRemoval: true)]
    private Collection $progressions;

    /**
     * @var Collection<int, UserModuleProgress>
     */
    #[ORM\OneToMany(targetEntity: UserModuleProgress::class, mappedBy: 'userId')]
    private Collection $userModuleProgress;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->registerAt = $now;
        $this->updateAt = $now;

        // --- C'EST CETTE PARTIE QUI CRÉE LES ENTITÉS PAR DÉFAUT ---
        $this->trophy = new Trophy();
        $this->trophy->setUser($this); // Essentiel pour lier le Trophy à ce User

        $this->userStreaks = new UserStreaks();
        $this->userStreaks->setUser($this); // Essentiel pour lier les Streaks à ce User
        $this->progressions = new ArrayCollection();
        $this->userModuleProgress = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->telephone;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getBirthdayAt(): ?\DateTimeImmutable
    {
        return $this->birthdayAt;
    }

    public function setBirthdayAt(?\DateTimeImmutable $birthdayAt): static
    {
        $this->birthdayAt = $birthdayAt;

        return $this;
    }

    public function getRegisterAt(): ?\DateTimeImmutable
    {
        return $this->registerAt;
    }

    public function setRegisterAt(\DateTimeImmutable $registerAt): static
    {
        $this->registerAt = $registerAt;

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

     public function __toString(): string
    {
        // Remplace 'name' ou 'title' par la propriété texte qui définit ton Sujet
        return $this->id ?? 'Sujet sans nom'; 
    }
    
    #[ORM\PreUpdate]  // ← s'exécute automatiquement avant chaque UPDATE
    public function onPreUpdate(): void
    {
        $this->updateAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection<int, Progression>
     */
    public function getProgressions(): Collection
    {
        return $this->progressions;
    }

    public function addProgression(Progression $progression): static
    {
        if (!$this->progressions->contains($progression)) {
            $this->progressions->add($progression);
            $progression->setUserId($this);
        }

        return $this;
    }

    public function removeProgression(Progression $progression): static
    {
        if ($this->progressions->removeElement($progression)) {
            // set the owning side to null (unless already changed)
            if ($progression->getUserId() === $this) {
                $progression->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserModuleProgress>
     */
    public function getUserModuleProgress(): Collection
    {
        return $this->userModuleProgress;
    }

    public function addUserModuleProgress(UserModuleProgress $userModuleProgress): static
    {
        if (!$this->userModuleProgress->contains($userModuleProgress)) {
            $this->userModuleProgress->add($userModuleProgress);
            $userModuleProgress->setUserId($this);
        }

        return $this;
    }

    public function removeUserModuleProgress(UserModuleProgress $userModuleProgress): static
    {
        if ($this->userModuleProgress->removeElement($userModuleProgress)) {
            // set the owning side to null (unless already changed)
            if ($userModuleProgress->getUserId() === $this) {
                $userModuleProgress->setUserId(null);
            }
        }

        return $this;
    }

}
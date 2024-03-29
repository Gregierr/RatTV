<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    #[Groups("user")]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(["user", "comment"])]
    #[ORM\Column(length: 32)]
    private ?string $login = null;

    #[Groups("user")]
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Groups("user")]
    #[ORM\Column(length: 32)]
    private ?string $email = null;

    #[Groups("user")]
    #[ORM\Column]
    private ?bool $isActive = null;

    #[Groups("user")]
    #[ORM\Column]
    private ?bool $isDeleted = null;

    #[Groups("user")]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activationToken = null;

    #[Groups("user")]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Video::class, orphanRemoval: true)]
    private Collection $videos;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Comment::class)]
    private Collection $comments;

    #[Groups("user")]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sessionToken = null;

    #[Groups("user")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sessionTokenExpireDate = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserTag::class)]
    private Collection $userTags;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->userTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(?string $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setUser($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getUser() === $this) {
                $video->setUser(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
    public function addComment(Comment $comment): self
    {
        if(!$this->comments->contains($comment)){
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }
    public function removeComment(Comment $comment):self
    {
        if($this->comments->removeElement($comment)) {
            //set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getSessionToken(): ?string
    {
        return $this->sessionToken;
    }

    public function setSessionToken(string $sessionToken): self
    {
        $this->sessionToken = $sessionToken;

        return $this;
    }

    public function getSessionTokenExpireDate(): ?\DateTimeInterface
    {
        return $this->sessionTokenExpireDate;
    }

    public function setSessionTokenExpireDate(\DateTimeInterface $sessionTokenExpireDate): self
    {
        $this->sessionTokenExpireDate = $sessionTokenExpireDate;

        return $this;
    }

    /**
     * @return Collection<int, UserTag>
     */
    public function getUserTags(): Collection
    {
        return $this->userTags;
    }

    public function addUserTag(UserTag $userTag): self
    {
        if (!$this->userTags->contains($userTag)) {
            $this->userTags->add($userTag);
            $userTag->setUser($this);
        }

        return $this;
    }

    public function removeUserTag(UserTag $userTag): self
    {
        if ($this->userTags->removeElement($userTag)) {
            // set the owning side to null (unless already changed)
            if ($userTag->getUser() === $this) {
                $userTag->setUser(null);
            }
        }

        return $this;
    }
}

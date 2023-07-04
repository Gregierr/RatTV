<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Video::class, inversedBy: 'tags')]
    private Collection $videos;

    #[ORM\OneToMany(mappedBy: 'tag', targetEntity: UserTag::class)]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        $this->videos->removeElement($video);

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
            $userTag->setTag($this);
        }

        return $this;
    }

    public function removeUserTag(UserTag $userTag): self
    {
        if ($this->userTags->removeElement($userTag)) {
            // set the owning side to null (unless already changed)
            if ($userTag->getTag() === $this) {
                $userTag->setTag(null);
            }
        }

        return $this;
    }
}

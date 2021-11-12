<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="integer")
     */
    private $authorId;

    /**
     * @ORM\ManyToMany(targetEntity=ReviewTags::class, inversedBy="reviews")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity=ReviewGroup::class, inversedBy="reviews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupId;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    public function setAuthorId(int $authorId): self
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * @return Collection|ReviewTags[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(ReviewTags $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(ReviewTags $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getGroupId(): ?ReviewGroup
    {
        return $this->groupId;
    }

    public function setGroupId(?ReviewGroup $groupId): self
    {
        $this->groupId = $groupId;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReviewTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:ReviewTagRepository::class)]
#[UniqueEntity(fields:["name"], message:"This tag was created")]
class ReviewTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private int $id;

    #[ORM\Column(type:"string", length:255)]
    private string $name;

    #[ORM\ManyToMany(targetEntity:Review::class, mappedBy:"tag")]
    private Collection $reviews;

    public function __construct(string $name = '')
    {
        $this->reviews = new ArrayCollection();
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /** @return Collection|Review[] */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->addTag($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            $review->removeTag($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}

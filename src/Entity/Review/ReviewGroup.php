<?php

declare(strict_types=1);

namespace App\Entity\Review;

use App\Repository\Review\ReviewGroupRepository;
use App\Services\Converter;
use App\Services\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewGroupRepository::class)]
class ReviewGroup
{
    public function __toString() : string
    { 
        return $this->name;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private int $id;

    #[ORM\OneToMany(targetEntity:Review::class, mappedBy:"group", orphanRemoval:true)]
    private Collection $reviews;

    #[ORM\Column(type: 'string', length: 25, options: ['default' => ''])]
    private $name;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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
            $review->setGroup($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getGroup() === $this) {
                $review->setGroup(null);
            }
        }

        return $this;
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
}

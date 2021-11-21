<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReviewRatingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRatingRepository::class)]
class ReviewRating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $value;

    #[ORM\ManyToOne(targetEntity: Review::class, inversedBy: 'reviewRatings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Review $review;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $valuer;

    public function __construct(Review $review, User $user, int $value)
    {
        $this->review = $review;
        $this->valuer = $user;
        $this->value = $value;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getReview(): Review
    {
        return $this->review;
    }

    public function setReview(?Review $Review): self
    {
        $this->review = $Review;

        return $this;
    }

    public function getValuer(): User
    {
        return $this->valuer;
    }

    public function setValuer(User $valuer): self
    {
        $this->valuer = $valuer;

        return $this;
    }
}

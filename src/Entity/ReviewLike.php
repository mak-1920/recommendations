<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReviewLikesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewLikesRepository::class)]
class ReviewLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $userEnt;

    #[ORM\ManyToOne(targetEntity: Review::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private Review $review;

    public function __construct()
    {
        $this->userEnt = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserEnt(): Collection
    {
        return $this->userEnt;
    }

    public function addUserEnt(User $userEnt): self
    {
        if (!$this->userEnt->contains($userEnt)) {
            $this->userEnt[] = $userEnt;
        }

        return $this;
    }

    public function removeUserEnt(User $userEnt): self
    {
        $this->userEnt->removeElement($userEnt);

        return $this;
    }

    public function getReview(): Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }
}

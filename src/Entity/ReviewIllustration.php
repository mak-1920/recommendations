<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReviewIllustrationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:ReviewIllustrationRepository::class)]
class ReviewIllustration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private int $id;

    #[ORM\Column(type:"string", length:255)]
    private string $img;

    #[ORM\ManyToOne(targetEntity:Review::class, inversedBy:"illustrations")]
    private $review;

    public function getId(): int
    {
        return $this->id;
    }

    public function getImg(): string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Review::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Review $review;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(type: 'datetimetz_immutable')]
    private DateTimeImmutable $time;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    public function getId(): int
    {
        return $this->id;
    }

    public function getReview(): Review
    {
        return $this->review;
    }

    public function setReview(Review $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(DateTimeImmutable $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }
}

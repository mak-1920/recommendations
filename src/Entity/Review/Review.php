<?php

declare(strict_types=1);

namespace App\Entity\Review;

use App\Entity\Users\User;
use App\Repository\Review\ReviewRepository;
use App\Services\Converter;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\Column(type: "text")]
    private string $text;

    #[ORM\ManyToMany(targetEntity: ReviewTag::class, inversedBy: "reviews", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: ReviewGroup::class, inversedBy: "reviews")]
    #[ORM\JoinColumn(nullable: false)]
    private ReviewGroup $group;

    #[ORM\OneToMany(targetEntity: ReviewIllustration::class, mappedBy: "review")]
    private Collection $illustrations;
    
    #[ORM\Column(type: "datetimetz_immutable")]
    private DateTimeImmutable $dateOfPublication;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "reviews")]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(type: 'integer')]
    private int $authorRating;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'review', targetEntity: ReviewRating::class, orphanRemoval: true)]
    private Collection $reviewRatings;

    #[ORM\Column(type: 'float', options: ['default' => 0])]
    private float $averageRating = 0;

    #[ORM\OneToMany(mappedBy: 'review', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->illustrations = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->reviewRatings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    
    /** @return Collection<ReviewTag> */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(ReviewTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(ReviewTag $tag): self
    {
        if($this->tags->removeElement($tag)){
            $tag->removeReview($this);
        }

        return $this;
    }

    public function setTags(array $tags): self
    {
        foreach($this->tags as $tag){
            $this->removeTag($tag);
        }

        foreach($tags as $tag){
            $this->addTag($tag);
        }

        return $this;
    }

    public function getGroup(): ReviewGroup
    {
        return $this->group;
    }

    public function setGroup(?ReviewGroup $group): self
    {
        $this->group = $group;

        return $this;
    }

    
    /** @return Collection<ReviewIllustration> */
    public function getIllustrations(): Collection
    {
        return $this->illustrations;
    }

    public function addIllustration(ReviewIllustration $illustration): self
    {
        if (!$this->illustrations->contains($illustration)) {
            $this->illustrations[] = $illustration;
            $illustration->setReview($this);
        }

        return $this;
    }

    public function removeIllustration(ReviewIllustration $illustration): self
    {
        if ($this->illustrations->removeElement($illustration)) {
            // set the owning side to null (unless already changed)
            if ($illustration->getReview() === $this) {
                $illustration->setReview(null);
            }
        }

        return $this;
    }

    public function getdateOfPublication(): \DateTimeImmutable
    {
        return $this->dateOfPublication;
    }

    public function setdateOfPublication(\DateTimeImmutable $dateOfPublication): self
    {
        $this->dateOfPublication = $dateOfPublication;

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

    public function getAuthorRating(): ?int
    {
        return $this->authorRating;
    }

    public function setAuthorRating(int $authorRating): self
    {
        $this->authorRating = $authorRating;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getLikes(): ?Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        $this->likes->removeElement($like);

        return $this;
    }

    /**
     * @return Collection|ReviewRating[]
     */
    public function getReviewRatings(): Collection
    {
        return $this->reviewRatings;
    }

    public function addReviewRating(ReviewRating $reviewRating): self
    {
        if (!$this->reviewRatings->contains($reviewRating)) {
            $this->reviewRatings[] = $reviewRating;
            $reviewRating->setReview($this);
            $this->changeAverageRating();
        }

        return $this;
    }

    public function removeReviewRating(ReviewRating $reviewRating): self
    {
        if ($this->reviewRatings->removeElement($reviewRating)) {
            // set the owning side to null (unless already changed)
            if ($reviewRating->getReview() === $this) {
                $reviewRating->setReview(null);
                $this->changeAverageRating();
            }
        }

        return $this;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    private function changeAverageRating(): void
    {
        if($this->reviewRatings->count() == 0){
            $this->averageRating = 0;
            return;
        }

        $sum = 0;
        foreach($this->reviewRatings as $rating){
            $sum += $rating->getValue();
        }
        $this->averageRating = round($sum / $this->reviewRatings->count(), 2);
    }

    public function getComments() : Collection
    {
        return $this->comments;
    }
    
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setReview($this);
        }

        return $this;
    }
}

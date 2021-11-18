<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReviewRepository;
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

    #[ORM\ManyToMany(targetEntity: ReviewTags::class, inversedBy: "reviews", cascade: ["persist"])]
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: ReviewGroup::class, inversedBy: "reviews")]
    #[ORM\JoinColumn(nullable: false)]
    private ReviewGroup $group;

    #[ORM\OneToMany(targetEntity: ReviewIllustration::class, mappedBy: "review")]
    private Collection $Illustrations;
    
    #[ORM\Column(type: "datetimetz_immutable")]
    private $DateOfPublication;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "reviews")]
    #[ORM\JoinColumn(nullable: false)]
    private $Author;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->Illustrations = new ArrayCollection();
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

    
    /** @return Collection<ReviewTags> */
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
        return $this->Illustrations;
    }

    public function addIllustration(ReviewIllustration $illustration): self
    {
        if (!$this->Illustrations->contains($illustration)) {
            $this->Illustrations[] = $illustration;
            $illustration->setReview($this);
        }

        return $this;
    }

    public function removeIllustration(ReviewIllustration $illustration): self
    {
        if ($this->Illustrations->removeElement($illustration)) {
            // set the owning side to null (unless already changed)
            if ($illustration->getReview() === $this) {
                $illustration->setReview(null);
            }
        }

        return $this;
    }

    public function getDateOfPublication(): \DateTimeImmutable
    {
        return $this->DateOfPublication;
    }

    public function setDateOfPublication(\DateTimeImmutable $DateOfPublication): self
    {
        $this->DateOfPublication = $DateOfPublication;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->Author;
    }

    public function setAuthor(User $Author): self
    {
        $this->Author = $Author;

        return $this;
    }
}

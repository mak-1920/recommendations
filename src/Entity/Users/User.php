<?php

declare(strict_types=1);

namespace App\Entity\Users;

use App\Entity\Review\Review;
use App\Repository\User\UserRepository;
use App\Services\Converter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name:"`user`")]
#[UniqueEntity(fields:["email"], message:"There is already an account with this email")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private int $id;

    #[ORM\Column(type:"string", length:180, unique:true)]
    private string $email;

    #[ORM\Column(type:"json")]
    private array $roles = [];

    /** @var string The hashed password */
    #[ORM\Column(type:"string", nullable:true)]
    private ?string $password;

    #[ORM\Column(type:"string", length:100)]
    private string $nickname;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $vk_id;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $google_id;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $facebook_id;

    #[ORM\OneToMany(targetEntity:Review::class, mappedBy:"author")]
    private Collection $reviews;

    private int $likesCount = -1;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function __toString() : string
    {
        return sprintf('%s (%d)', $this->getNickname(), $this->getLikesCount());
    }

    public function getLikesCount() : int
    {
        if($this->likesCount != -1){
            return $this->likesCount;
        }
        $sum = 0;
        /** @var Review $review */
        foreach($this->getReviews() as $review){
            $sum += $review->getLikes()->count();
        }
        return $sum;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->nickname;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getVkId(): ?string
    {
        return $this->vk_id;
    }

    public function setVkId(?string $vk_id): self
    {
        $this->vk_id = $vk_id;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->google_id;
    }

    public function setGoogleId(?string $google_id): self
    {
        $this->google_id = $google_id;

        return $this;
    }

    static public function Create(
        string $email,
        string $socialName,
        string $socialID,
        string $nickname
    ) : User
    {
        $user = new User();
        $user->setEmail($email);
        switch($socialName){
        case 'google':
            $user->setGoogleId($socialID);
            break;
        case 'vk':
            $user->setVkId($socialID);
            break;
        case 'facebook':
            $user->setFacebookId($socialID);
            break;
        default:
            break;
        }
        $user->setNickname($nickname);
        $user->setRoles([self::ROLE_USER]);
        return $user;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebook_id;
    }

    public function setFacebookId(string $facebookId): self
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setAuthor($this);
        }

        return $this;
    }
}

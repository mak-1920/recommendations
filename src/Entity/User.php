<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vk_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vk_token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $google_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $google_token;

    /**
     * @param string $clientId
     * @param string $email
     * @param string $username
     * @param string $oauthType
     * @param array $roles
     */
    public function __construct(
        string $email,
        string $username
    ) {
        $this->email = $email;
        $this->username = $username;
        $this->roles = [self::ROLE_USER];
    }

    // /**
    //  * @param int $clientId
    //  * @param string $email
    //  * @param string $username
    //  *
    //  * @return User
    //  */
    // public static function fromVKRequest(
    //     int $clientId,
    //     string $email,
    //     string $username
    // ): User
    // {
    //     $user = new self(
    //         $email,
    //         $username,
    //         [self::ROLE_USER]
    //     );
    //     $user->vk_id = $clientId;
    //     return $user;
    // }

    // /**
    //  * @param string $clientId
    //  * @param string $email
    //  * @param string $username
    //  *
    //  * @return User
    //  */
    // public static function fromGoogleRequest(
    //     string $clientId,
    //     string $email,
    //     string $username
    // ): User
    // {
    //     $user = new self(
    //         $email,
    //         $username,
    //         [self::ROLE_USER]
    //     );
    //     $user->google_id = $clientId;
    //     return $user;
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
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
        return (string) $this->email;
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
    public function getPassword(): string
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

    public function getNickname(): ?string
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

    public function getVkToken(): ?string
    {
        return $this->vk_token;
    }

    public function setVkToken(?string $vk_token): self
    {
        $this->vk_token = $vk_token;

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

    public function getGoogleToken(): ?string
    {
        return $this->google_token;
    }

    public function setGoogleToken(?string $google_token): self
    {
        $this->google_token = $google_token;

        return $this;
    }
}

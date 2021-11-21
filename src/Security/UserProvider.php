<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Users\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     *
     * @return mixed|UserInterface
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername(string $username)
    {
        return $this->userRepository->loadUserByUsername($username);
    }

    /**
     * @param int $username
     *
     * @return mixed|UserInterface
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(int $id)
    {
        return $this->userRepository->loadUserByIdentifier($id);
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw  new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported. ', get_class($user))
            );
        }

        return $user;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return $class === 'App\Entity\User';
    }
}
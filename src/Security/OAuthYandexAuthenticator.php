<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Users\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Aego\OAuth2\Client\Provider\YandexResourceOwner;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuthYandexAuthenticator extends AbstractOAuthAuthenticator
{
    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        UserRepository $userRepository
    )
    {
        $this->init($clientRegistry, $em, $userRepository, 'yandex');
    }

    public function getUser(mixed $credentials, UserProviderInterface $userProvider) : ?UserInterface
    {
        /** @var YandexResourceOwner $yandexUser */
        $yandexUser = $this->getClient()
            ->fetchUserFromToken($credentials);

        $email = $yandexUser->getEmail();
        
        /** @var User $existingUser */
        $existingUser = $this->userRepository
            ->findOneBy(['yandex_id' => $yandexUser->getId()]);

        if ($existingUser) {
            return $existingUser;
        }

        /** @var User $user */
        $user = $this->userRepository
            ->findOneBy(['email' => $email]);

        if (!$user) {
            $user = User::Create($email, 'yandex', $yandexUser->getId(), $yandexUser->getName());
        } else {
            $user->setGoogleId($yandexUser->getId());
        }
            
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
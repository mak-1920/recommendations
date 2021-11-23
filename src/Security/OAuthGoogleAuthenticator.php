<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Users\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuthGoogleAuthenticator extends AbstractOAuthAuthenticator
{
    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        UserRepository $userRepository
    )
    {
        $this->init($clientRegistry, $em, $userRepository, 'google');
    }

    public function getUser(mixed $credentials, UserProviderInterface $userProvider) : ?UserInterface
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getClient()
            ->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();

        /** @var User $existingUser */
        $existingUser = $this->userRepository
            ->findOneBy(['google_id' => $googleUser->getId()]);

        if ($existingUser) {
            return $existingUser;
        }

        /** @var User $user */
        $user = $this->userRepository
            ->findOneBy(['email' => $email]);

        if (!$user) {
            $user = User::Create($email, 'google', $googleUser->getId(), $googleUser->getName());

            $this->em->persist($user);
            $this->em->flush();
        } else {
            $user->setGoogleId($googleUser->getId());
        }

        return $user;
    }
}
<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Users\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuthGitHubAuthenticator extends AbstractOAuthAuthenticator
{
    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        UserRepository $userRepository
    )
    {
        $this->init($clientRegistry, $em, $userRepository, 'github');
        throw new Exception('github init');
    }

    public function getUser(mixed $credentials, UserProviderInterface $userProvider) : ?UserInterface
    {
        /** @var GithubResourceOwner $ghUser */
        $ghUser = $this->getClient()
            ->fetchUserFromToken($credentials);
        $email = $ghUser->getEmail();

        /** @var User $existingUser */
        $existingUser = $this->userRepository
            ->findOneBy(['github_id' => $ghUser->getId()]);

        if ($existingUser) {
            return $existingUser;
        }

        /** @var User $user */
        $user = $this->userRepository
            ->findOneBy(['email' => $email]);
        dump([$ghUser, $email]);
        throw new Exception();

        if (!$user) {
            $user = User::Create($email, 'github', (string)$ghUser->getId(), $ghUser->getName());

            $this->em->persist($user);
            $this->em->flush();
        } else {
            $user->setGoogleId($ghUser->getId());

            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }
}
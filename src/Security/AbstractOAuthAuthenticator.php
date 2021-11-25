<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

abstract class AbstractOAuthAuthenticator extends SocialAuthenticator
{
    protected ClientRegistry $clientRegistry;
    
    protected EntityManagerInterface $em;
    
    protected UserRepository $userRepository;

    protected string $social;

    protected function init(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        string $social
    ) : void
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->social = $social;
    }

    public function start(Request $request, AuthenticationException $authException = null) : RedirectResponse
    {
        return new RedirectResponse(
            '/connect/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === $this->social.'_auth';
    }

    public function getCredentials(Request $request) : ?AccessToken
    {
        return $this->fetchAccessToken($this->getClient());
    }

    abstract public function getUser(mixed $credentials, UserProviderInterface $userProvider) : ?UserInterface;

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response
    {
        return null;
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        $providerKey
    ): ?Response
    {
        return null;
    }

    public function getClient(): OAuth2Client
    {
        dump([$this->clientRegistry->getClient($this->social), $this->social]);
        throw new Exception('oauth getclient');
        return $this->clientRegistry->getClient($this->social);
    }
}
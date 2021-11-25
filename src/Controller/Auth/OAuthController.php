<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\BaseController;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends BaseController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function redirectToGoogleConnect(ClientRegistry $clientRegistry) : Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile'], []);
    }

    #[Route("/google/auth", name: "google_auth")]
    public function connectGoogleCheck() : Response
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => "User not found!"]);
        } else {
            return $this->redirectToRoute('reviews');
        }
    }

    #[Route('/connect/github', name: 'connect_github_start')]
    public function redirectToGHConnect(ClientRegistry $clientRegistry) : Response
    {
        return $clientRegistry
            ->getClient('github')
            ->redirect(['email', 'profile'], []);
    }

    #[Route("/github/auth", name: "github_auth")]
    public function connectGHCheck() : Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {
            return $this->redirectToRoute('reviews');
        }
    }
}

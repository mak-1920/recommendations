<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\BaseController;
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

    #[Route('/connect/facebook', name: 'connect_facebook_start')]
    public function redirectToFacebookConnect(ClientRegistry $clientRegistry) : Response
    {
        return $clientRegistry
            ->getClient('facebook')
            ->redirect(['email', 'profile'], []);
    }

    #[Route("/facebook/auth", name: "facebook_auth")]
    public function connectFacebookCheck() : Response
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => "User not found!"]);
        } else {
            return $this->redirectToRoute('rewies');
        }
    }
}

<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function redirectToGoogleConnect(ClientRegistry $clientRegistry) : RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile'], []);
    }

    #[Route("/google/auth", name: "google_auth")]
    public function connectGoogleCheck() : JsonResponse|RedirectResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => "User not found!"]);
        } else {
            return $this->redirectToRoute('main');
        }
    }

    #[Route('/connect/vk', name: 'connect_vk_start')]
    public function redirectToVKConnect(ClientRegistry $clientRegistry) : RedirectResponse
    {
        return $clientRegistry
            ->getClient('vk')
            ->redirect(['email', 'profile'], []);
    }

    #[Route("/vk/auth", name: "vk_auth")]
    public function connectVKCheck() : JsonResponse|RedirectResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => "User not found!"]);
        } else {
            return $this->redirectToRoute('main');
        }
    }
}

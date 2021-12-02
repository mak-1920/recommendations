<?php

declare(strict_types=1);

namespace App\Controller\Settings;

use App\Services\ThemeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    #[Route(
        '/settings/theme/{theme}',
        name: 'settings_theme',
    )]
    public function setTheme(string $theme, Request $request, ThemeManager $themeManager): Response
    {
        $response = new RedirectResponse($request->headers->get('referer'));

        $themeManager->setTheme($theme, $response);

        return $response;
    }
}

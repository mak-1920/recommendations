<?php

declare(strict_types=1);

namespace App\Controller\AP;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPanelController extends BaseController
{
    #[Route('/{_locale<%app.locales%>}/ap', name: 'apanel')]
    public function index(): Response
    {
        $users = $this->userRepository->findBy([], ['id' => 'ASC']);

        return $this->render('ap/index.html.twig', [
            'users' => $users,
        ]);
    }
}

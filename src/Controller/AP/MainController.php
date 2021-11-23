<?php

namespace App\Controller\AP;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/{_locale<%app.locales%>}/ap', name: 'apanel')]
    public function index(): Response
    {
        return $this->render('ap/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Users;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    #[Route(
        '/{_locale<%app.locales%>}/id{id}', 
        name: 'user_page',
        requirements: ['id' => '\d+'],    
    )]
    public function userPage(int $id): Response
    {
        $user = $this->userRepository->find($id);

        if($user == null){
            throw new NotFoundHttpException();
        }

        return $this->render('user/page.html.twig', [
            'user' => $user,
        ]);
    }
}

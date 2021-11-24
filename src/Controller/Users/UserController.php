<?php

declare(strict_types=1);

namespace App\Controller\Users;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
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
    public function userPage(int $id, Request $request): Response
    {
        $user = $this->userRepository->find($id);
        $orderBy = $request->get('type');
        if($orderBy == null){
            $orderBy = $this->sortedTypes[0];
        }

        if($user == null){
            throw new NotFoundHttpException();
        }

        return $this->render('user/page.html.twig', [
            'user' => $user,
            'sortedTypes' => $this->sortedTypes,
            'sortType' => $orderBy,
        ]);
    }
}

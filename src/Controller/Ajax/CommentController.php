<?php

declare(strict_types=1);

namespace App\Controller\Ajax;

use App\Controller\BaseController;
use App\Entity\Review\Comment;
use App\Repository\Review\CommentRepository as ReviewCommentRepository;
use App\Repository\Review\ReviewRepository as ReviewReviewRepository;
use App\Services\ESIndexer;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Existence;

class CommentController extends BaseController
{
    #[Route(
        '/{_locale<%app.locales%>}/ajax/comment/page/{page}', 
        name: 'comment', 
        requirements: ['page' => '\d+'], 
        methods: ['POST'],
    )]
    public function index(int $page, Request $request) : Response
    {
        $review = $this->reviewRepository->find($request->get('param'));
        $comments = $this->commentRepository->getPageComment(
            $page,
            $review ?? [],
        );

        return $this->render('ajax/comment/page.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route(
        '/ajax/comment/create', 
        'comment_create', 
        methods: ['POST'],
    )]
    public function create(Request $request, ESIndexer $eSIndexer) : Response
    {
        $reviewId = (int) $request->request->get('reviewId');
        $text = $request->request->get('text');
        
        $comment = $this->commentRepository->addComment($reviewId, $this->getUser(), $text);

        if($comment instanceof Comment){
            $eSIndexer->edit($comment->getReview());
        }

        return $this->json(['result' => Response::HTTP_CREATED]);
    }

    #[Route(
        '/ajax/comment/remove', 
        name: 'comment_remove', 
        methods: ['POST'],
    )]
    public function remove(Request $request, ESIndexer $eSIndexer) : Response
    {
        $commentId = (int) $request->request->get('id');
        $user = $this->getUser();

        $comment = $this->commentRepository->remove($commentId, $user);

        if($comment instanceof Comment){
            $eSIndexer->edit($comment->getReview());
        }

        return $this->json(['result' => $comment != null]);
    }
}

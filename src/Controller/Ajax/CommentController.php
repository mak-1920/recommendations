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
        '/{_locale<%app.locales%>}/ajax/comment/page', 
        name: 'comment', 
        methods: ['POST'],
    )]
    public function index(Request $request) : Response
    {
        $review = $this->reviewRepository->find($request->get('param'));
        $lastId = (int)$request->request->get('lastId');

        $comments = $this->commentRepository->getPageComment(
            $lastId,
            $review ?? [],
        );

        $end = end($comments);
        return $this->json([
            'html' => $this->render('ajax/comment/page.html.twig', [
                'comments' => $comments,
            ]),
            'lastId' => $end ? $end->getId() : 0,
            'result' => true,
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

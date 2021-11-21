<?php

declare(strict_types=1);

namespace App\Controller\Ajax;

use App\Controller\BaseController;
use App\Entity\Review\Comment;
use App\Repository\Review\CommentRepository as ReviewCommentRepository;
use App\Repository\Review\ReviewRepository as ReviewReviewRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends BaseController
{
    #[Route('/ajax/comment/page/{page}', name: 'comment', requirements: ['page' => '\d+'], methods: ['GET'])]
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

    #[Route('/ajax/comment/create', 'comment_create', methods: ['POST'])]
    public function create(Request $request) : Response
    {
        $reviewId = $request->request->get('reviewId');
        $review = $this->reviewRepository->find($reviewId);
        $text = $request->request->get('text');

        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setReview($review);
        $comment->setText($text);
        $comment->setTime(new DateTimeImmutable());

        $em = $this->getDoctrine()->getManager();
        
        $em->persist($review);
        $em->persist($comment);

        $em->flush();

        return $this->json(['result' => Response::HTTP_CREATED]);
    }
}

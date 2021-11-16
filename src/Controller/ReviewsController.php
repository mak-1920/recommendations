<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Review;
use App\Entity\ReviewTags;
use App\Form\ReviewCreatorType;
use App\Repository\ReviewRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Extra\Markdown\MarkdownExtension;

class ReviewsController extends AbstractController
{
    private ReviewRepository $reviewRepository;

    public function __construct(
        ReviewRepository $reviewRepository, 
    ){
        $this->reviewRepository = $reviewRepository;
    }

    #[Route('/', name: 'reviews')]
    public function index(): Response
    {
        return $this->render('reviews/index.html.twig', [
            'reviews' => $this->reviewRepository->findAll(),
        ]);
    }

    #[Route('/ajax/reviews/page/{page}', name: 'review_page', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function page(int $page, ReviewRepository $reviewRepository) : Response
    {
        $reviews = $reviewRepository->getLastReviews($page);
        return $this->render('reviews/page.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/review/create', name: 'review_create')]
    public function create(Request $request) : Response
    {
        $review = new Review();

        $form = $this->createForm(ReviewCreatorType::class, $review);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $review->setAuthor($this->getUser());
            $review->setDateOfPublication(new DateTimeImmutable());

            foreach($form->tags as $tag)
                $review->tag;

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute($this->generateUrl('review_id', ['id' => $review->getId()]));
        }

        return $this->renderForm('reviews/creat.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/review/tag-{tag}', name: 'review_tag')]
    public function reviewTag(ReviewTags $tag) : Response
    {
        return $this->render($tag);
    }

    #[Route('/review/id{id}', name: 'review_id', requirements: ['id' => '\d+'])]
    public function reviewId(Review $review) : Response
    {
        return $this->render('reviews/review.html.twig', [
            'review' => $review,
        ]);
    }
}

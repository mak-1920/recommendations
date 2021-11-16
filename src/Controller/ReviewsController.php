<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Review;
use App\Entity\ReviewTags;
use App\Form\ReviewCreatorType;
use App\Repository\ReviewRepository;
use App\Repository\ReviewTagsRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    public function create(Request $request, ReviewTagsRepository $reviewTagsRepository) : Response
    {
        $review = new Review();

        
        $form = $this->createForm(ReviewCreatorType::class, $review);
        // if($form->isSubmitted()){
        //     $tags = $request
        // }
        $form->handleRequest($request);

        // \var_dump($request->request->get('review_creator')['tags']);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();

            $review->setAuthor($this->getUser());
            $review->setDateOfPublication(new DateTimeImmutable());

            // foreach(explode(', ', $form->get('tagsOfString')->getData()) as $tag){
            //     // if($tags->)
            //     $review->addTag($tag);

            // }

            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirect($this->generateUrl(
                'review_id', 
                ['id' => $review->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ));
        }

        return $this->renderForm('reviews/creat.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/review/tag-{tag}', name: 'review_tag')]
    public function reviewTag(ReviewTags $tag) : Response
    {
        return $this->render($tag->getName());
    }

    #[Route(
        '/review/id{id}', 
        name: 'review_id', 
        requirements: ['id' => '\d+'])]
    public function reviewId(Review $review) : Response
    {
        return $this->render('reviews/review.html.twig', [
            'review' => $review,
        ]);
    }
}

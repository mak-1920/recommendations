<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Review;
use App\Entity\ReviewRating;
use App\Entity\ReviewTags;
use App\Form\ReviewCreatorType;
use App\Repository\ReviewRatingRepository;
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
    private array $sortedTypes = ['relevance', 'rating'];

    public function __construct(
        private ReviewRepository $reviewRepository, 
    ){
    }

    #[Route('/', name: 'reviews')]
    public function index(Request $request): Response
    {
        $sortedType = $request->get('type') ?? $this->sortedTypes[0];

        return $this->render('reviews/index.html.twig', [
            'sortedType' => $this->sortedTypes,
            'selectedSortType' => $sortedType,
        ]);
    }

    #[Route('/ajax/reviews/page/{page}', name: 'review_page', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function page(int $page, ReviewRepository $reviewRepository, Request $request) : Response
    {
        $type = $request->get('param');
        switch($type){
            case $this->sortedTypes[1]: 
                $reviews = $reviewRepository->getLastReviews($page, 'authorRating');
                break;
            default:
                $reviews = $reviewRepository->getLastReviews($page, 'id');
                break;
        }
        return $this->render('reviews/page.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/review/edit/id{id}', name: 'review_edit', requirements: ['id' => '\d+'], defaults: ['id' => '0'])]
    #[Route('/review/create', name: 'review_create')]
    public function create(Request $request, ReviewTagsRepository $reviewTagsRepository, Review $review = null) : Response
    {
        $title = 'Review edit';
        if($review == null) {
            $review = new Review();
            $title = 'Review create';
        }

        $form = $this->createForm(ReviewCreatorType::class, $review);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            
            /** @var array $formData */
            $formData = $request->request->get('review_creator');

            if(isset($formData['tags'])){
                $tags = $reviewTagsRepository->getEntityFromStringArray($formData['tags']);
                $review->setTags($tags);
            }

            $review->setAuthor($this->getUser());
            $review->setDateOfPublication(new DateTimeImmutable());

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
            'title' => $title,
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
    public function reviewId(int $id) : Response
    {
        $review = $this->reviewRepository->findByID($id);
        $isLiked = $review->getLikes()->contains($this->getUser());
        $ratingValue = $review->getReviewRatings()->first($this->getUser())?->getValue() ?? -1;
        return $this->render('reviews/review.html.twig', [
            'review' => $review,
            'isLiked' => $isLiked,
            'ratingValue' => $ratingValue,
        ]);
    }

    #[Route(
        'ajax/review/like/id{id}',
        name: 'review_like',
        requirements: ['id' => '\d+'],
        methods: ['GET'])]
    public function reviewLike(int $id) : Response
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Review $review */
        $review = $this->reviewRepository->findOneWithLikes($id);
        $user = $this->getUser();
        $result = true;
        if($review->getLikes()->contains($user)){
            $review->removeLike($user);
            $result = false;
        } else {
            $review->addLike($user);
        }
        $em->persist($review);
        $em->flush();
        return $this->json(['result' => $result, 'likesCount' => $review->getLikes()->count()]);
    }

    #[Route('ajax/review/set-rating/id{id}', 
        name: 'review_set_rating',
        requirements: ['id' => '\d+'],
        methods: ['POST'])]
    public function reviewSetRating(int $id, ReviewRatingRepository $reviewRatingRepository, Request $request) : Response
    {
        $value = (int) $request->request->get('value');
        $review = $this->reviewRepository->findByID($id);
        $rating = $reviewRatingRepository->findOneByUserAndReview($this->getUser(), $review);
        $add = true;

        if($rating == null){
            $rating = new ReviewRating($review, $this->getUser(), $value);
            $review->addReviewRating($rating);
        } else {
            $review->removeReviewRating($rating);
            $add = false;
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($rating);
        $em->persist($review);
        $em->flush();

        return $this->json(['add' => $add, 'rateValue' => $review->getAverageRating()]);
    }
}
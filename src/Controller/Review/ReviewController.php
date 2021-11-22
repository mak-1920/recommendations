<?php

declare(strict_types=1);

namespace App\Controller\Review;

use App\Controller\BaseController;
use App\Entity\Review\Review;
use App\Entity\Review\ReviewRating;
use App\Form\Review\ReviewCreatorType;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ReviewController extends BaseController
{
    #[Route('/{_locale<%app.locales%>}/', name: 'reviews')]
    public function index(Request $request) : Response
    {
        $sortedType = $request->get('type') ?? $this->sortedTypes[0];
        
        $tags = $this->reviewTagRepository->findAllOrderByName();
        
        return $this->render('review/index.html.twig', [
            'sortedType' => $this->sortedTypes,
            'selectedSortType' => $sortedType,
            'selectedSortTypeName' => $this->translator->trans($sortedType),
            'tags' => $tags,
        ]);
    }

    #[Route('/{_locale<%app.locales%>}/review/edit/id{id}', name: 'review_edit', requirements: ['id' => '\d+'], defaults: ['id' => '0'])]
    #[Route('/{_locale<%app.locales%>}/review/create', name: 'review_create')]
    public function create(Request $request, Review $review = null) : Response
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
                $tags = $this->reviewTagRepository->getEntityFromStringArray($formData['tags']);
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

        return $this->renderForm('review/creat.html.twig', [
            'form' => $form,
            'title' => $title,
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/review/id{id}', 
        name: 'review_id', 
        requirements: ['id' => '\d+'])]
    public function reviewId(int $id) : Response
    {
        $review = $this->reviewRepository->findByID($id);
        $isLiked = $review->getLikes()->contains($this->getUser());
        // $rating = $reviewRatingRepository->findOneByUserAndReview($this->getUser(), $review);
        /** @var ?ReviewRating $rating */
        $rating = $review->getReviewRatings()
            ->filter(fn (ReviewRating $r) => $r->getValuer() == $this->getUser())
            ->first();
        $ratingValue = $rating == null ? -1 : $rating->getValue();
        return $this->render('review/review.html.twig', [
            'review' => $review,
            'isLiked' => $isLiked,
            'ratingValue' => $ratingValue,
        ]);
    }
}
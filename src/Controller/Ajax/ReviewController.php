<?php

declare(strict_types=1);

namespace App\Controller\Ajax;

use App\Controller\BaseController;
use App\Entity\Review\ReviewRating;
use App\Repository\Review\ReviewRatingRepository;
use App\Repository\Review\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends BaseController
{    
    #[Route('/ajax/sortable-reviews/page/{page}', name: 'review_sortable_page', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function reviewSortablePage(int $page, Request $request) : Response
    {
        $type = $request->get('param');
        switch($type){
            case $this->sortedTypes[1]: 
                $reviews = $this->reviewRepository->getLastReviews($page, 'averageRating');
                break;
            default:
                $reviews = $this->reviewRepository->getLastReviews($page);
                break;
        }
        return $this->render('ajax/review/page.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/ajax/reviews-by-tag/page/{page}', name: 'review_page_by_tag', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function reviewPageByTag(int $page, Request $request) : Response
    {
        $name = mb_substr($request->get('param'), 4);

        $reviews = $this->reviewRepository->findByTagName($name, $page);

        return $this->render('ajax/review/page.html.twig', [
            'reviews' => $reviews,
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
    public function reviewSetRating(int $id, Request $request) : Response
    {
        $value = (int) $request->request->get('value');
        $review = $this->reviewRepository->findByID($id);
        $rating = $this->reviewRatingRepository->findOneByUserAndReview($this->getUser(), $review);
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

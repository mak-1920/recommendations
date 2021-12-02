<?php

declare(strict_types=1);

namespace App\Controller\Ajax;

use App\Controller\BaseController;
use App\Entity\Review\ReviewRating;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends BaseController
{    
    #[Route(
        '/{_locale<%app.locales%>}/ajax/sortable-reviews/page/{page}', 
        name: 'review_sortable_page', 
        requirements: ['page' => '\d+'], 
        methods: ['POST'])]
    public function reviewSortablePage(int $page, Request $request) : Response
    {
        $type = $request->request->get('param');
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

    #[Route(
        '/{_locale<%app.locales%>}/ajax/reviews-by-tag/page/{page}',
        name: 'review_page_by_tag', 
        requirements: ['page' => '\d+'], 
        methods: ['POST'])]
    public function reviewPageByTag(int $page, Request $request) : Response
    {
        $name = mb_substr($request->request->get('param'), 4);

        $reviews = $this->reviewRepository->findByTagName($name, $page);

        return $this->render('ajax/review/page.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/ajax/reviews-by-user/page/{page}',
        name: 'review_by_user',
        requirements: ['page' => '\d+'],
        methods: ['POST'],
    )]
    public function reviewByUser(int $page, Request $request) : Response
    {
        $params = explode(',', $request->request->get('param'));

        switch($params[1]){
            case $this->sortedTypes[1]: 
                $reviews = $this->reviewRepository->findByUser((int)$params[0], $page, 'averageRating');
                break;
            default:
                $reviews = $this->reviewRepository->findByUser((int)$params[0], $page);
                break;
        }

        return $this->render('ajax/review/page.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route(
        '/ajax/review/like/id{id}',
        name: 'review_like',
        requirements: ['id' => '\d+'],
        methods: ['POST'])]
    public function reviewLike(int $id) : Response
    {
        $user = $this->getUser();
        
        $review = $this->reviewRepository->addOrRemoveLike($id, $user);

        return $this->json([
            'result' => $review->getLikes()->contains($user), 
            'likesCount' => $review->getLikes()->count()
        ]);
    }

    #[Route('/ajax/review/set-rating/id{id}', 
        name: 'review_set_rating',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function reviewSetRating(int $id, Request $request) : Response
    {
        $user = $this->getUser();
        $value = (int) $request->request->get('value');

        $res = $this->reviewRepository->setRating($id, $user, $value);

        return $this->json([
            'add' => $res['result'], 
            'rateValue' => $res['review']->getAverageRating()
        ]);
    }
}

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
        '/{_locale<%app.locales%>}/ajax/sortable-reviews/page', 
        name: 'review_sortable_page', 
        methods: ['POST'])]
    public function reviewSortablePage(Request $request) : Response
    {
        $lastId = (int)$request->request->get('lastId');
        $type = $request->request->get('param');
        $reviews = [];
        
        switch($type){
            case $this->sortedTypes[1]: 
                $reviews = $this->reviewRepository->getLastReviews($lastId, 'averageRating');
                break;
            default:
                $reviews = $this->reviewRepository->getLastReviews($lastId);
                break;
        }
        $end = end($reviews);
        return $this->json([
            'html' => $this->render('ajax/review/page.html.twig', [
                'reviews' => $reviews,
            ]),
            'lastId' => $end ? $end->getId() : 0,
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/ajax/reviews-by-tag/page',
        name: 'review_page_by_tag', 
        methods: ['POST'])]
    public function reviewPageByTag(Request $request) : Response
    {
        $name = mb_substr($request->request->get('param'), 4);
        $lastId = (int)$request->request->get('lastId');

        $reviews = $this->reviewRepository->findByTagName($name, $lastId);

        $end = end($reviews);
        return $this->json([
            'html' => $this->render('ajax/review/page.html.twig', [
                'reviews' => $reviews,
            ]),
            'lastId' => $end ? $end->getId() : 0,
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/ajax/reviews-by-user/page',
        name: 'review_by_user',
        methods: ['POST'],
    )]
    public function reviewByUser(Request $request) : Response
    {
        $lastId = (int)$request->request->get('lastId');
        $params = explode(',', $request->request->get('param'));

        switch($params[1]){
            case $this->sortedTypes[1]: 
                $reviews = $this->reviewRepository->findByUser((int)$params[0], $lastId, 'averageRating');
                break;
            default:
                $reviews = $this->reviewRepository->findByUser((int)$params[0], $lastId);
                break;
        }

        $end = end($reviews);
        return $this->json([
            'html' => $this->render('ajax/review/page.html.twig', [
                'reviews' => $reviews,
            ]),
            'lastId' => $end ? $end->getId() : 0,
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

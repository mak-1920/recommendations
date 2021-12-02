<?php

declare(strict_types=1);

namespace App\Controller\Ajax;

use App\Services\Searcher;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private Searcher $searcher,
    )
    {
    }

    #[Route(
        '/{_locale<%app.locales%>}/ajax/search/page',
        'review_search_page',
        methods: ['POST'],
    )]
    public function search(Request $request) : Response
    {
        $query = $request->get('param');
        $page = (int)$request->request->get('lastId');
        if($page == -1) {
            $page = 1;
        }

        $reviews = $this->searcher->getResultByPage($query, $page);

        return $this->json([
            'html' => $this->render('ajax/review/page.html.twig', [
                'reviews' => $reviews,
            ]),
            'lastId' => $reviews == null ? 0 : $page + 1,
        ]);
    }
}

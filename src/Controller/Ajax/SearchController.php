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
        '/{_locale<%app.locales%>}/ajax/search/page/{page}',
        'review_search_page',
        requirements: ['page' => '\d+'],
        methods: ['GET'],
    )]
    public function search(Request $request, int $page) : Response
    {
        $query = $request->get('param');

        return $this->render('ajax/review/page.html.twig', [
            'reviews' => $this->searcher->getResultByPage($query, $page),
        ]);
    }
}

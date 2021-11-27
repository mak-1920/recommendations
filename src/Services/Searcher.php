<?php

declare(strict_types=1);

namespace App\Services;

use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Pagerfanta\Pagerfanta;

class Searcher {
    public function __construct(
        private PaginatedFinderInterface $finder,
    )
    {
    }

    public function getResultCount($query) : int
    {
        return $this->finder
            ->findPaginated($query)
            ->count();
    }

    public function getResultByPage($query, $page) : ?Pagerfanta
    {
        $result = $this->finder->findPaginated($query);
        $result->setMaxPerPage(20);
        if($page > $result->getNbPages()){
            return null;
        }
        $result->setCurrentPage($page);

        return $result;
    }
}
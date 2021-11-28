<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Review\Review;
use FOS\ElasticaBundle\Persister\ObjectPersister;

class ESIndexer extends AbstractController
{
    public function __construct(
        private ObjectPersister $reviewPersister,
    ){}

    public function new($review) : void
    {
        $this->reviewPersister->insertOne($review);
    }

    public function edit(Review $review) : void
    {
        $this->reviewPersister->replaceOne($review);
    }
    
    public function delete(Review $review) : void
    {
        $this->reviewPersister->deleteOne($review);
    }
}
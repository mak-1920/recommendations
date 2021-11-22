<?php

declare(strict_types=1);

namespace App\Controller\Review;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends BaseController
{
    #[Route('/{_locale<%app.locales%>}/review/tag-{tagName}', name: 'review_tag', requirements: ['name' => '.+'])]
    public function byTag(?string $tagName = null) : Response
    {
        $tags = $this->reviewTagRepository->findAllOrderByName();
        
        return $this->render('review/tag/tag.html.twig', [
            'sortedType' => $this->sortedTypes,
            'selectedSortType' => '',
            'tags' => $tags,
            'tagName' => $tagName,
        ]);
    }
}

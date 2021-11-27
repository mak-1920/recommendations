<?php

declare(strict_types=1);

namespace App\Controller\Review;

use App\Controller\BaseController;
use App\Entity\Review\Review;
use App\Entity\Review\ReviewRating;
use App\Entity\Users\User;
use App\Form\Review\ReviewCreatorType;
use App\Services\Searcher;
use DateTimeImmutable;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        /** @var User $user */
        $user = $this->getUser();
        $iscreating = true;

        if($review == null) {
            $review = new Review();
        } else {
            if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') 
                    && $review->getAuthor() != $user) {
                throw new AccessDeniedException();
            }
            $user = $review->getAuthor();
            $iscreating = false;
        }

        $form = $this->createForm(ReviewCreatorType::class, $review);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $tags = [];
            
            /** @var array $formData */
            $formData = $request->request->get('review_creator');

            if(isset($formData['tags'])){
                $tags = $this->reviewTagRepository->getEntityFromStringArray($formData['tags']);
            }

            $reviewId = $this->reviewRepository->createOrUpdate($review, $user, $tags);

            return $this->redirect($this->generateUrl(
                'review_id', 
                ['id' => $reviewId],
                UrlGeneratorInterface::ABSOLUTE_URL
            ));
        }

        return $this->renderForm('review/edit.html.twig', [
            'form' => $form,
            'isCreating' => $iscreating,
            'reviewId' => $iscreating ? 0 : $review->getId(),
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/review-id{id}', 
        name: 'review_id', 
        requirements: ['id' => '\d+'])]
    public function reviewId(int $id) : Response
    {
        $review = $this->reviewRepository->findByID($id);
        if($review == null){
            throw new NotFoundHttpException();
        }
        $isLiked = $review->getLikes()->contains($this->getUser());
        
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
    
    #[Route(
        '{_locale<%app.locales%>}/review/remove-id{id}',
        name: 'review_remove',
        requirements: ['id' => '\d+'],
    )]
    public function remove(int $id) : Response
    {
        $res = $this->reviewRepository->remove($id, $this->getUser());

        if(!$res) {
            throw new AccessDeniedException();
        }

        return $this->redirectToRoute('reviews');
    }

    #[Route(
        '/{_locale<%app.locales%>}/search',
        name: 'review_search',
        methods: ['GET'],
    )]
    public function search(Request $request, Searcher $searcher) : Response
    {
        $query = $request->get('q') ?? '';

        return $this->render('review/search.html.twig', [
            'query' => $query,
            'count' => $searcher->getResultCount($query),
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewCreatorType;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewsController extends AbstractController
{
    private ReviewRepository $reviewRepository;

    public function __construct(
        ReviewRepository $reviewRepository, 
    ){
        $this->reviewRepository = $reviewRepository;
    }

    #[Route('/', name: 'reviews')]
    public function index(): Response
    {
        return $this->render('reviews/index.html.twig', [
            'reviews' => $this->reviewRepository->findAll(),
        ]);
    }

    #[Route('/review/create', name: 'review_create')]
    public function create(Request $request) : Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewCreatorType::class, $review);
        $form->handleRequest($request);

        return $this->renderForm('reviews/creat.html.twig', [
            'form' => $form,
        ]);
    }
}

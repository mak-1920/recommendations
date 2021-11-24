<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Review\CommentRepository;
use App\Repository\Review\ReviewRatingRepository;
use App\Repository\Review\ReviewRepository;
use App\Repository\Review\ReviewTagRepository;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    protected array $sortedTypes = ['relevance', 'rating'];

    public function __construct(
        protected ReviewRepository $reviewRepository, 
        protected ReviewTagRepository $reviewTagRepository,
        protected ReviewRatingRepository $reviewRatingRepository,
        protected CommentRepository $commentRepository,
        protected TranslatorInterface $translator,
        protected UserRepository $userRepository,
    ){
    }
}
<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\Review;
use App\Entity\Review\ReviewRating;
use App\Entity\Users\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReviewRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewRating[]    findAll()
 * @method ReviewRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewRating::class);
    }

    public function findOneByUserAndReview(User $user, Review $review) : ?ReviewRating
    {
        return $this->createQueryBuilder('r')
            ->where('r.valuer = :user AND r.review = :review')
            ->setParameter('user', $user)
            ->setParameter('review', $review)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}

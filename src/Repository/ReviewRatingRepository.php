<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Review;
use App\Entity\ReviewRating;
use App\Entity\User;
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
        $qb = $this->createQueryBuilder('r');
        return $qb
            ->where('r.valuer = :user AND r.Review = :review'
                // $qb->expr()->andX(
                //     $qb->expr()->eq('r.valuer', ':user'),
                //     $qb->expr()->eq('r.review', ':reveiw'),
                // )
            )
            ->setParameter('user', $user)
            ->setParameter('review', $review)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return ReviewRating[] Returns an array of ReviewRating objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReviewRating
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ReviewLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReviewLikes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewLikes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewLikes[]    findAll()
 * @method ReviewLikes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewLike::class);
    }

    // /**
    //  * @return ReviewLikes[] Returns an array of ReviewLikes objects
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
    public function findOneBySomeField($value): ?ReviewLikes
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

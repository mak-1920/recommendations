<?php

namespace App\Repository;

use App\Entity\Review;
use App\Entity\ReviewGroup;
use App\Entity\ReviewTags;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @return Review[] Returns an array of Review objects
     */
    public function getLastReviews(int $page, string $sortedField) : array
    {
        $query = $this->createQueryBuilder('r')
            ->select('r, u, g, t')
            ->orderBy('r.'.$sortedField, 'DESC')
            ->join('r.Author', 'u')
            ->join('r.group', 'g')
            ->join('r.tags', 't')
            ->setFirstResult(($page - 1) * 10)
            ->setMaxResults(10)
            ;

        $paginator = new Paginator($query, true);

        $result = [];

        foreach($paginator as $post) {
            $result[] = $post;
        }

        return $result;
    }

    /*
    public function findOneBySomeField($value): ?Review
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

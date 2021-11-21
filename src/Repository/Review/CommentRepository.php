<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\Comment;
use App\Entity\Review\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getPageComment(int $page, Review $review) : array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c, u, r')
            ->join('c.author', 'u')
            ->join('c.review', 'r')
            ->where('r = :review')
            ->setFirstResult(($page - 1) * 20)
            ->setMaxResults(20)
            ->orderBy('c.id', 'DESC')
            ->setParameter('review', $review);

        $paginator = new Paginator($query, true);

        $res = [];
        foreach($paginator as $post){
            $res[] = $post;
        }

        return $res;
    }
}
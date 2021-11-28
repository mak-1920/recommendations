<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\Comment;
use App\Entity\Review\Review;
use App\Entity\Users\User;
use DateTimeImmutable;
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
            ->select('c, u, r, ur')
            ->join('c.author', 'u')
            ->join('c.review', 'r')
            ->leftJoin('u.reviews', 'ur')
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

    public function addComment(int $reviewId, User $user, string $text) : ?Comment
    {
        $em = $this->_em;

        $reviewRepository = $em->getRepository(Review::class);
        $review = $reviewRepository->find($reviewId);

        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setReview($review);
        $comment->setText($text);
        $comment->setTime(new DateTimeImmutable());

        $review->addComment($comment);

        $em->persist($review);
        $em->persist($comment);

        $em->flush();

        return $comment;
    }

    public function findById(int $id) : ?Comment
    {
        return $this->createQueryBuilder('c')
            ->select('c, u, r')
            ->leftJoin('c.author', 'u')
            ->leftJoin('c.review', 'r')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function remove(int $commentId, User $user) : ?Comment
    {
        $em = $this->_em;

        $comment = $this->findById($commentId);

        if($comment == null 
            || !($comment->getAuthor() == $user || array_search(User::ROLE_ADMIN, $user->getRoles()) !== false)){
            return null;
        }

        $comment->getReview()->removeComment($comment);

        $em->remove($comment);
        $em->flush();

        return $comment;
    }
}

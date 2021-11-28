<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\Review;
use App\Entity\Review\ReviewRating;
use App\Entity\Users\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Egulias\EmailValidator\Warning\AddressLiteral;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    private const REVIEW_ON_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    private function getMainQuery() : QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->from(Review::class, 'r')
            ->select('r, u, g, t, rait, ur, url, i, c')
            ->leftJoin('r.author', 'u')
            ->leftJoin('u.reviews', 'ur')
            ->leftJoin('ur.likes', 'url')
            ->leftjoin('r.group', 'g')
            ->leftjoin('r.tags', 't')
            ->leftJoin('r.reviewRatings', 'rait')
            ->leftJoin('r.illustrations', 'i')
            ->leftJoin('r.comments', 'c')
            ;
    }

    public function findByID($id) : ?Review
    {
        return $this->getMainQuery()
            ->addSelect('tr')
            ->leftJoin('t.reviews', 'tr')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getLastReviews(int $page, string $sortedBy = null) : array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->getMainQuery('r');
        if($sortedBy != null){
            $qb->OrderBy('r.'.$sortedBy, 'DESC')
                ->addOrderBy('r.id', 'DESC');
        } else {
            $qb->orderBy('r.id', 'DESC');
        }
        $qb->setFirstResult(($page - 1) * self::REVIEW_ON_PAGE)
            ->setMaxResults(self::REVIEW_ON_PAGE)
            ;

        $paginator = new Paginator($qb, true);

        $result = [];

        foreach($paginator as $post) {
            $result[] = $post;
        }

        return $result;
    }

    public function findByUser(int $userId, int $page, string $orderBy = null) : array
    {
        $qb = $this->getMainQuery()
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->setFirstResult(($page - 1) * self::REVIEW_ON_PAGE)
            ->setMaxResults(self::REVIEW_ON_PAGE);
        if($orderBy != null){
            $qb->orderBy('r.'.$orderBy, 'DESC')
                ->addOrderBy('r.id', 'DESC');
        } else {
            $qb->orderBy('r.id', 'DESC');
        }

        $paginator = new Paginator($qb, true);
        $result = [];

        foreach($paginator as $post){
            $result[] = $post;
        }

        return $result;
    }

    public function createOrUpdate(Review $review, User $user, array $tags) : int
    {
        $entityManager = $this->_em;
            
        $review->setTags($tags);
        $review->setAuthor($user);
        
        $review->setDateOfPublication(new DateTimeImmutable());

        $entityManager->persist($review);
        $entityManager->flush();

        return $review->getId();
    }

    public function addOrRemoveLike(int $reviewId, User $user) : Review
    {
        $em = $this->_em;
        /** @var ReviewRepository $reviewRepository */
        $reviewRepository = $em->getRepository(Review::class);
        /** @var Review $review */
        $review = $reviewRepository->findOneWithLikes($reviewId);

        $result = true;
        if($review->getLikes()->contains($user)){
            $review->removeLike($user);
            $result = false;
        } else {
            $review->addLike($user);
        }
        $em->persist($review);
        $em->flush();
        
        return $review;
    }

    public function setRating(int $reviewId, User $user, int $value) : array
    {
        $em = $this->_em;
        /** @var ReviewRatingRepository $reviewRatingRepository */
        $reviewRatingRepository = $em->getRepository(ReviewRating::class);
        
        $review = $this->findByID($reviewId);
        $rating = $reviewRatingRepository->findOneByUserAndReview($user, $review);
        $result = true;

        if($rating == null){
            $rating = new ReviewRating($review, $user, $value);
            $review->addReviewRating($rating);
        } else {
            $review->removeReviewRating($rating);
            $result = false;
        }
        
        $em->persist($rating);
        $em->flush();

        return ['review' => $review, 'result' => $result];
    }

    public function findOneWithLikesAndTags(int $id) : ?Review
    {
        return $this->createQueryBuilder('r')
            ->select('r, l')
            ->leftJoin('r.likes', 'l')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByTagName(string $name, int $page) : array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r')
            ->innerJoin('r.tags', 't')
            ->where('t.name = :tagName')
            ->setParameter('tagName', $name)
            ->orderBy('r.id', 'DESC')
            ->setFirstResult(($page - 1) * self::REVIEW_ON_PAGE)
            ->setMaxResults(self::REVIEW_ON_PAGE)
            ;

        $paginator = new Paginator($qb, true);

        $ids = [];

        foreach($paginator as $post) {
            $ids[] = $post->getId();
        }

        return $this->getMainQuery()
            ->where('r.id in (:ids)')
            ->orderBy('r.id', 'DESC')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
            ;
    }

    public function remove(int $id, User $user) : bool
    {
        $em = $this->_em;

        $review = $this->findByID($id);

        if($review == null 
            || !($review->getAuthor() == $user || array_search(User::ROLE_ADMIN, $user->getRoles()) !== false)){
            return false;
        }

        foreach($review->getTags() as $tag){
            $review->removeTag($tag);
            dump($tag);
        }
        dump($review);
        $em->flush();

        $em->remove($review);
        $em->flush();

        return true;
    }
}

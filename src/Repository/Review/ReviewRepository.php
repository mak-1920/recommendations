<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\Review;
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

    private function getMainQuery(string $alias) : QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->from(Review::class, $alias)
            ->select('r, u, g, t, rait, ur, url')
            ->leftJoin('r.author', 'u')
            ->leftJoin('u.reviews', 'ur')
            ->leftJoin('ur.likes', 'url')
            ->leftjoin('r.group', 'g')
            ->leftjoin('r.tags', 't')
            ->leftJoin('r.reviewRatings', 'rait')
            ;
    }

    public function findByID($id) : ?Review
    {
        return $this->getMainQuery('r')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @return Review[] Returns an array of Review objects
     */
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

    public function findOneWithLikes(int $id) : ?Review
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

        return $this->getMainQuery('r')
            ->where('r.id in (:ids)')
            ->orderBy('r.id', 'DESC')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
            ;
    }
}

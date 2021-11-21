<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Review;
use App\Entity\ReviewGroup;
use App\Entity\ReviewRating;
use App\Entity\ReviewTags;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Egulias\EmailValidator\Warning\AddressLiteral;

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

    public function findByID($id) : ?Review
    {
        return $this->createQueryBuilder('r')
            ->select('r, u, g, t, rait')
            ->leftjoin('r.Author', 'u')
            ->leftjoin('r.group', 'g')
            ->leftjoin('r.tags', 't')
            ->leftJoin('r.reviewRatings', 'rait')
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
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from(Review::class, 'r')
            ->select('r, u, g, t, l, rait');
        if($sortedBy != null){
            $qb->OrderBy('r.'.$sortedBy, 'DESC')
                ->addOrderBy('r.id', 'DESC');
        } else {
            $qb->orderBy('r.id', 'DESC');
        }
        $qb->leftjoin('r.Author', 'u')
            ->leftjoin('r.group', 'g')
            ->leftjoin('r.tags', 't')
            ->leftJoin('r.reviewRatings', 'rait')
            ->leftJoin('r.likes', 'l')
            ->setFirstResult(($page - 1) * 10)
            ->setMaxResults(10)
            ;

        $paginator = new Paginator($qb, true);

        $result = [];

        foreach($paginator as $post) {
            $result[] = $post;
        }
        // dump($result);

        return $result;
    }

    public function findOneWithLikes(int $id) : ?Review
    {
        $queryBuilder = $this->createQueryBuilder('r');

        return $queryBuilder
            ->select('r, l')
            ->leftJoin('r.likes', 'l')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
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

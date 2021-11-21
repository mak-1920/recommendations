<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\ReviewGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReviewGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewGroup[]    findAll()
 * @method ReviewGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewGroup::class);
    }
}

<?php

namespace App\Repository;

use App\Entity\ReviewTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReviewTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewTags[]    findAll()
 * @method ReviewTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewTags::class);
    }

    // /**
    //  * @return ReviewTags[] Returns an array of ReviewTags objects
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
    public function findOneBySomeField($value): ?ReviewTags
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

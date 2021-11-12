<?php

namespace App\Repository;

use App\Entity\ReviewGroup;
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

    // /**
    //  * @return ReviewGroup[] Returns an array of ReviewGroup objects
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
    public function findOneBySomeField($value): ?ReviewGroup
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

<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\ReviewIllustration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReviewIllustration|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewIllustration|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewIllustration[]    findAll()
 * @method ReviewIllustration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewIllustrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewIllustration::class);
    }

    // /**
    //  * @return ReviewIllustration[] Returns an array of ReviewIllustration objects
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
    public function findOneBySomeField($value): ?ReviewIllustration
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

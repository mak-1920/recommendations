<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ReviewTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
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

    // public function createAndGetTagsOfEntity(array $tagsOfArray) : ArrayCollection
    // {
    //     /**
    //      * @var ArrayCollection $tags
    //      */
    //     $tags = $this->createQueryBuilder('t')
    //         ->where('name in :tags')
    //         ->setParameter('tags', array_values($tagsOfArray))
    //         ->getQuery()
    //         ->getResult();
    //     $tags->map(function($tag){
    //         $tagsOfArray
    //     });
    // }

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

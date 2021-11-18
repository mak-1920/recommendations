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

    public function getTagsNames() : array
    {
        return $this->createQueryBuilder('t')
            ->select('t.name')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getEntityFromStringArray(array $tagsArrFromRequest) : array
    {
        $tagsArr = [];
        foreach($tagsArrFromRequest as $tag){
            $tagsArr[] = $tag['name'];
        }
        $tagsEnt = $this->createQueryBuilder('t')
            ->where('t.name IN (:tag_names)')
            ->setParameter('tag_names', $tagsArr, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();
        foreach($tagsEnt as $tag){
            $key = array_search($tag->getName(), $tagsArr);
            unset($tagsArr[$key]);
        }
        foreach($tagsArr as $tag){
            $tagsEnt[] = new ReviewTags($tag);
        }
        return $tagsEnt;
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

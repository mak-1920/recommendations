<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ReviewTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReviewTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewTag[]    findAll()
 * @method ReviewTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewTag::class);
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
            $tagsEnt[] = new ReviewTag($tag);
        }
        return $tagsEnt;
    }

    public function findAllOrderByName() : array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

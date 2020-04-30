<?php

namespace App\Repository;

use App\Entity\CommentPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentPost[]    findAll()
 * @method CommentPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentPost::class);
    }

    public function getCommentsByPost(int $post)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.user', 'user')
            ->addSelect('user')
            ->leftJoin('c.post', 'post')
            ->where('post.id = :id')
            ->setParameter('id', $post)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    // /**
    //  * @return CommentPost[] Returns an array of CommentPost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentPost
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

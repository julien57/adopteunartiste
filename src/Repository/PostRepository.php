<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findLastPost()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.publishedAt', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();
    }

    public function getPostOffset($start, $limit)
    {
        $query = $this->createQueryBuilder('p')
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    public function getLast10Posts()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.photo', 'photo')
            ->addSelect('photo')
            ->leftJoin('p.reacts', 'reacts')
            ->addSelect('reacts')
            ->leftJoin('p.user', 'user')
            ->addSelect('user')
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getLast10PostsFil(User $user)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.photo', 'photo')
            ->addSelect('photo')
            ->leftJoin('p.reacts', 'reacts')
            ->addSelect('reacts')
            ->leftJoin('p.user', 'user')
            ->addSelect('user')
            ->leftJoin('user.adopts', 'adopts')
            ->leftJoin('p.userGroup', 'userGroup')
            ->where('adopts.userTo = :user')
            ->orWhere('adopts.userFrom = :user')
            ->orWhere('userGroup.members = :user')
            ->setParameter('user', $user)
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findPostById(int $post)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.photo', 'photo')
            ->addSelect('photo')
            ->leftJoin('p.user', 'user')
            ->addSelect('user')
            ->leftJoin('p.userGroup', 'userGroup')
            ->addSelect('userGroup')
            ->where('p.id = :id')
            ->setParameter('id', $post)
            ->getQuery()
            ->getArrayResult();
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

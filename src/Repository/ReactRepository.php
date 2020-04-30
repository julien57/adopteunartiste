<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\React;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method React|null find($id, $lockMode = null, $lockVersion = null)
 * @method React|null findOneBy(array $criteria, array $orderBy = null)
 * @method React[]    findAll()
 * @method React[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, React::class);
    }

    public function getReactPerPost(Post $post)
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->select('r.type, COUNT(r.id) as count')
            ->leftJoin('r.users', 'users')
            ->where('r.post = :post')
            ->setParameter('post', $post)
            ->groupBy('r.type')
            ->orderBy('count', 'ASC');

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function getReactPerPostJS(int $post)
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->select('r.type, COUNT(r.id) as count')
            ->leftJoin('r.post', 'post')
            ->where('post.id = :id')
            ->setParameter('id', $post)
            ->groupBy('r.type')
            ->orderBy('count', 'ASC');

        return $qb
            ->getQuery()
            ->getArrayResult();
    }

    public function GetCountAllReact(Post $post)
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->select('COUNT(r.id)')
            ->where('r.post = :post')
            ->setParameter('post', $post);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return React[] Returns an array of React objects
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
    public function findOneBySomeField($value): ?React
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

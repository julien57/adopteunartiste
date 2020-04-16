<?php

namespace App\Repository;

use App\Entity\DomainArtist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DomainArtist|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomainArtist|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomainArtist[]    findAll()
 * @method DomainArtist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomainArtist::class);
    }

    // /**
    //  * @return DomainArtist[] Returns an array of DomainArtist objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DomainArtist
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function searchGroup(string $searchValue)
    {
        return $this->createQueryBuilder('u')
            ->where('u.name LIKE :name')
            ->setParameter('name', '%'.$searchValue.'%')
            ->getQuery()
            ->getResult();
    }

    public function searchGroupSearchByName(string $searchValue)
    {
        return $this->createQueryBuilder('u')
            ->where('u.name LIKE :name')
            ->setParameter('name', '%'.$searchValue.'%')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getLastUsersInGroup(Group $group)
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.members', 'members')
            ->where('g.id = :id')
            ->setParameter('id', $group->getId())
            ->select('members.avatar as avatar')
            ->groupBy('avatar')
            ->getQuery()
            ->getResult();
    }

    public function findByCountMembers()
    {
        return $this->createQueryBuilder('g')
            ->addSelect('COUNT(members) AS HIDDEN personCount')
            ->leftJoin('g.members', 'members')
            ->groupBy('g')
            ->orderBy('personCount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCountMembersSearch(string $searchValue)
    {
        return $this->createQueryBuilder('g')
            ->where('g.name LIKE :name')
            ->setParameter('name', '%'.$searchValue.'%')
            ->addSelect('COUNT(members) AS HIDDEN personCount')
            ->leftJoin('g.members', 'members')
            ->groupBy('g')
            ->orderBy('personCount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function searchGroupGroups(string $searchValue)
    {
        return $this->createQueryBuilder('u')
            ->where('u.name LIKE :name')
            ->setParameter('name', '%'.$searchValue.'%')
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Group[] Returns an array of Group objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Group
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

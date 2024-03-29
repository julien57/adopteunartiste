<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function searchUser(string $searchValue)
    {
        return $this->createQueryBuilder('u')
            ->where('u.pseudo LIKE :pseudo')
            ->setParameter('pseudo', '%'.$searchValue.'%')
            ->getQuery()
            ->getResult();
    }

    public function getLastUsersInGroup(Group $group)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.groups', 'groups')
            ->where('groups = :group')
            ->setParameter('group', $group)
            ->andWhere('u.avatar IS NOT NULL')
            ->orderBy('u.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }


    public function searchGroupGroups(string $searchValue)
    {
        return $this->createQueryBuilder('u')
            ->where('u.pseudo LIKE :name')
            ->setParameter('name', '%'.$searchValue.'%')
            ->orderBy('u.subscribedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMembersByActif()
    {
        return $this->createQueryBuilder('u')
            ->addSelect('COUNT(posts) AS HIDDEN postsCount, COUNT(adopts) AS HIDDEN adoptsCount')
            ->leftJoin('u.posts', 'posts')
            ->leftJoin('u.adopts', 'adopts')
            ->groupBy('u')
            ->orderBy('postsCount', 'DESC')
            ->addOrderBy('adoptsCount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMembersByActifWithSearch(string $searchValue)
    {
        return $this->createQueryBuilder('u')
            ->where('u.pseudo LIKE :name')
            ->setParameter('name', '%'.$searchValue.'%')
            ->addSelect('COUNT(posts) AS HIDDEN postsCount, COUNT(adopts) AS HIDDEN adoptsCount')
            ->leftJoin('u.posts', 'posts')
            ->leftJoin('u.adopts', 'adopts')
            ->groupBy('u')
            ->orderBy('postsCount', 'DESC')
            ->addOrderBy('adoptsCount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

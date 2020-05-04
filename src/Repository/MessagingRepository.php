<?php

namespace App\Repository;

use App\Entity\Messaging;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Messaging|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messaging|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messaging[]    findAll()
 * @method Messaging[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messaging::class);
    }

    public function getMessages(User $user)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.sendTo', 'sendTo')
            ->leftJoin('m.sendFor', 'sendFor')
            ->select('sendFor.id as sendForId')
            ->where('m.sendTo != :user')
            ->andWhere('m.sendFor = :user')
            ->orWhere('m.sendTo = :user')
            ->andWhere('m.sendFor != :user')
            ->setParameter('user', $user)
            ->groupBy('sendForId')
            ->getQuery()
            ->getResult();
    }

    public function getIfMessagingExist(User $user1, User $user2)
    {
        return $this->createQueryBuilder('m')
            ->where('m.sendTo = :user1')
            ->andWhere('m.sendFor = :user2')
            ->orWhere('m.sendTo = :user2')
            ->andWhere('m.sendFor = :user1')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->getQuery()
            ->getResult();
    }

    public function getLastMessage(User $user1, User $user2)
    {
        return $this->createQueryBuilder('m')
            ->where('m.sendTo = :user1')
            ->andWhere('m.sendFor = :user2')
            ->orWhere('m.sendTo = :user2')
            ->andWhere('m.sendFor = :user1')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->andWhere('m.message IS NOT NULL')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getMessageChat(User $user1, User $user2)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.sendFor', 'sendFor')
            ->leftJoin('m.sendTo', 'sendTo')
            ->where('sendTo = :user1 AND sendFor = :user2')
            ->orWhere('sendTo = :user2 AND sendFor = :user1')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->andWhere('m.message IS NOT NULL')
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Messaging[] Returns an array of Messaging objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Messaging
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

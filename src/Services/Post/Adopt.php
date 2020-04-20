<?php

namespace App\Services\Post;

use App\Entity\User;
use App\Repository\AdoptRepository;

class Adopt
{
    /**
     * @var AdoptRepository
     */
    private $adoptRepository;

    public function __construct(AdoptRepository $adoptRepository)
    {
        $this->adoptRepository = $adoptRepository;
    }

    public function getAdoptAxist(User $userFrom, User $userTo)
    {
        return $this->adoptRepository->findOneBy(['userFrom' => $userFrom, 'userTo' => $userTo]);
    }

    public function getAllAdoptUser(User $user)
    {
        return $this->adoptRepository->getFriendsUser($user);
    }
}

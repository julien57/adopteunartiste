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
        $resultOne = $this->adoptRepository->findOneBy(['userFrom' => $userFrom, 'userTo' => $userTo]);
        if (!$resultOne) {
            $resultOne = $this->adoptRepository->findOneBy(['userFrom' => $userTo, 'userTo' => $userFrom]);
        }

        return $resultOne;
    }

    public function getAllAdoptUser(User $user)
    {
        return $this->adoptRepository->getFriendsUser($user);
    }
}

<?php

namespace App\Services\Post;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\AdoptRepository;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;

class Adopt
{
    /**
     * @var AdoptRepository
     */
    private $adoptRepository;
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(AdoptRepository $adoptRepository, GroupRepository $groupRepository, UserRepository $userRepository)
    {
        $this->adoptRepository = $adoptRepository;
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
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

    public function getlast5usersGroup(Group $group)
    {
        return $this->groupRepository->getLastUsersInGroup($group);
    }
}

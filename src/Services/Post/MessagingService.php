<?php

namespace App\Services\Post;

use App\Entity\User;
use App\Repository\MessagingRepository;
use App\Repository\UserRepository;

class MessagingService
{
    /**
     * @var MessagingRepository
     */
    private $messagingRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(MessagingRepository $messagingRepository, UserRepository $userRepository)
    {
        $this->messagingRepository = $messagingRepository;
        $this->userRepository = $userRepository;
    }

    public function getLastMessage(User $sendTo, User $sendFor)
    {
        return $this->messagingRepository->getLastMessage($sendTo, $sendFor);
    }

    public function listUser(User $userMe)
    {
        $usersId = $this->messagingRepository->getMessages($userMe);

        $users = [];
        foreach ($usersId as $id) {
            $user = $this->userRepository->find($id['sendForId']);
            $users[] = $user;
        }
        $firstUser = $users[0];
        $messagings = $this->messagingRepository->getMessageChat($firstUser, $userMe);

        return [
            'users' => $users,
            'firstUser' => $users[0],
            'messagings' => $messagings
        ];
    }
}

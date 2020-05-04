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
}

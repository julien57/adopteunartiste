<?php

namespace App\Services\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\ReactRepository;

class React
{
    /**
     * @var ReactRepository
     */
    private $reactRepository;

    public function __construct(ReactRepository $reactRepository)
    {
        $this->reactRepository = $reactRepository;
    }

    public function getReactPost(Post $post)
    {
        return $this->reactRepository->getReactPerPost($post);
    }

    public function countReactPost(Post $post)
    {
        return $this->reactRepository->GetCountAllReact($post);
    }

    public function getIfReactUser(Post $post, User $user)
    {
        $reacts = $this->reactRepository->findBy(['post' => $post]);
        $reactExist = null;
        foreach ($reacts as $react) {
            if ($react->getUsers()->contains($user)) {
                $reactExist = $react;
            }
        }

        if (!$reactExist) {
            return false;
        } else {
            return true;
        }
    }
}

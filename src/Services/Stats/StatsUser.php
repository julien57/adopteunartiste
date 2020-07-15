<?php

namespace App\Services\Stats;

use App\Entity\User;
use App\Repository\PostRepository;

class StatsUser
{
    const STATS_TYPE_DAY = 'day';
    const STATS_TYPE_MONTH = 'month';
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function findTypeStatsUser(User $user)
    {
        $dateNow = new \DateTime('now');
        $dateSubscribedUser = $user->getSubscribedAt();
        $interval = date_diff($dateNow, $dateSubscribedUser);

        if ($interval->m > 1) {
            return self::STATS_TYPE_MONTH;
        }

        return self::STATS_TYPE_DAY;
    }

    public function getStatsMonthUser(User $user, array $stats)
    {
        // Past Month
        $dateNowLess30Days = new \DateTime('-30 days');

        $dateNowLess30DaysToModify = new \DateTime('-30 days');
        $pastMonth = $dateNowLess30DaysToModify->modify('-1 month');

        $postsPastMonth = $this->postRepository->findPostBetweenDates($dateNowLess30Days, $pastMonth, $user);
        $percentPostsCreated = (count($user->getPosts()) * 100) / count($postsPastMonth);

        $countReactionLastMonth = 0;
        $countCommentLastMonth = 0;
        foreach ($postsPastMonth as $post) {
            $countReactionLastMonth = $countReactionLastMonth + count($post->getReacts());
            $countCommentLastMonth = $countCommentLastMonth + count($post->getCommentPosts());
        }

        if ($countReactionLastMonth !== 0) {
            $countReactionLastMonth = ($stats['reactions'] * 100) / $countReactionLastMonth;
        }
        if ($countCommentLastMonth !== 0) {
            $countCommentLastMonth = ($stats['comments'] * 100) / $countCommentLastMonth;
        }

        return [
            'percentPostCreated' => $percentPostsCreated,
            'percentReaction' => $countReactionLastMonth,
            'percentComment' => $countCommentLastMonth
        ];
    }

    public function getStatsDayUser(User $user)
    {
        $dateNow = new \DateTime();
        $daNowLess30Days = new \DateTime('-30 days');

        $reactions = 0;
        $comments = 0;
        $photos = 0;
        $posts = $this->postRepository->findPostBetweenDates($dateNow, $daNowLess30Days, $user);
        $allPosts = $this->postRepository->findBy(['user' => $user]);

        foreach ($posts as $post) {
            $reactions = $reactions + count($post->getReacts());
            $comments = $comments + count($post->getCommentPosts());
            if ($post->getPhoto()) {
                $photos = $photos + count($post->getPhoto());
            }
        }

        $totalReactions = 0;
        $totalComments = 0;
        foreach ($allPosts as $post) {
            $totalReactions = $totalReactions + count($post->getReacts());
            $totalComments = $totalComments + count($post->getCommentPosts());
        }

        return [
            'views' => $user->getNbVisit(),
            'postCreated' => count($user->getPosts()),
            'reactions' => $reactions,
            'comments' => $comments,
            'photos' => $photos,
            'totalReaction' => $totalReactions,
            'totalComment' => $totalComments,
        ];
    }
}

<?php

namespace App\Controller\Front\General;

use App\Repository\UserRepository;
use App\Services\Stats\StatsUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StatistiquesController extends AbstractController
{
    /**
     * @Route("/mes-stats", name="front_general_stats")
     */
    public function list(UserRepository $userRepository, StatsUser $statsUser)
    {
        $user = $userRepository->find($this->getUser()->getId());

        $statsType = $statsUser->findTypeStatsUser($user);
        if ($statsType === StatsUser::STATS_TYPE_MONTH) {
            $stats = $statsUser->getStatsDayUser($user);
            $percent = $statsUser->getStatsMonthUser($user, $stats);
        } else {
            $stats = $statsUser->getStatsDayUser($user);
            $percent = null;
        }

        return $this->render('front/general/stats.html.twig', [
            'user' => $user,
            'stats' => $stats,
            'percent' => $percent ? $percent : null
        ]);
    }
}

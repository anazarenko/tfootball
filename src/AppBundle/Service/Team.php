<?php

namespace AppBundle\Service;

use AppBundle\Entity\Game as GameEntity;
use AppBundle\Entity\Statistics;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Team
{
    protected $entityManager;
    protected $container;

    public function __construct(EntityManager $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * @param $firstTeam
     * @param $secondTeam
     * @param $errorMsg
     * @return bool
     */
    public function isValidTeams($firstTeam, $secondTeam, &$errorMsg)
    {
        if (count($firstTeam) != count($secondTeam)) {
            $errorMsg = 'Count of member must be equal';
            return false;
        }

        foreach ($firstTeam as $member) {
            if (in_array($member, $secondTeam)) {
                $errorMsg = 'Player do not repeated!';
                return false;
            }
        }

        return true;
    }

    /**
     * Find team. If this team is null, then create new team and return it
     * @param array $teamMembers array of team member entities
     * @return Team|array
     */
    public function getTeam($teamMembers)
    {
        // Find team in database
        $team = $this->entityManager->getRepository('AppBundle:Team')->findTeamByMembers($teamMembers);

        // If team does not exist, create them
        if (!$team) {
            $team = new \AppBundle\Entity\Team();
            $team->setPlayerCount(count($teamMembers));

            $names = array();

            /** @var \AppBundle\Entity\User $user */
            foreach ($teamMembers as $user) {
                $team->addUser($user);
                $user->addTeam($team);

                $names[] = $user->getUsername();
            }

            shuffle($names);
            $team->setPlayerNames($names);

            $this->entityManager->persist($team);
            $this->entityManager->flush();

            $statistics = new Statistics();
            $statistics->setTeam($team);
            $statistics->setMonth(0);
            $statistics->setYear(0);

            $this->entityManager->persist($statistics);
            $this->entityManager->flush();

            $team->addStatistic($statistics);
            $this->entityManager->flush();
        }

        return $team;

    }

    /**
     * Update team statistics if game is confirmed
     *
     * @param GameEntity $game
     * @param int $action
     */
    public function updateStatistics(GameEntity $game, $action = Statistics::ACTION_ADD)
    {
        $statsRepository = $this->entityManager->getRepository('AppBundle:Statistics');
        $statisticsService = $this->container->get('app.statistic_service');
        $isDraw = false;
        // Stats by current month
        $winnerTeamStats = null;
        // Stats by current month
        $defeatTeamStats = null;
        // Stats by all period
        $winnerTeamStatsAll = null;
        // Stats by all period
        $defeatTeamStatsAll = null;
        $date = $game->getGameDate();
        // Stats by current month
        $firstTeamStats = $statsRepository
            ->getStatistic(
                $game->getFirstTeam(),
                (int)$date->format('m'),
                (int)$date->format('Y')
            );
        // Stats by all period
        $firstTeamStatsAll = $statsRepository->getStatistic($game->getFirstTeam());
        // Stats by current month
        $secondTeamStats = $statsRepository
            ->getStatistic(
                $game->getSecondTeam(),
                (int)$date->format('m'),
                (int)$date->format('Y')
            );
        // Stats by all period
        $secondTeamStatsAll = $statsRepository->getStatistic($game->getSecondTeam());

        if ($game->getResult() == GameEntity::RESULT_DRAW) {
            $isDraw = true;
        } else {
            if ($game->getResult() == GameEntity::RESULT_FIRST_WINNER) {
                $winnerTeamStats = $firstTeamStats;
                $defeatTeamStats = $secondTeamStats;
                $winnerTeamStatsAll = $firstTeamStatsAll;
                $defeatTeamStatsAll = $secondTeamStatsAll;
            } else {
                $winnerTeamStats = $secondTeamStats;
                $defeatTeamStats = $firstTeamStats;
                $winnerTeamStatsAll = $secondTeamStatsAll;
                $defeatTeamStatsAll = $firstTeamStatsAll;
            }
        }

        if ($action == Statistics::ACTION_ADD) {

            if ($isDraw) {
                $firstTeamStats->addDrawn();
                $secondTeamStats->addDrawn();
                $firstTeamStatsAll->addDrawn();
                $secondTeamStatsAll->addDrawn();
            } else {
                $winnerTeamStats->addWon();
                $winnerTeamStatsAll->addWon();
                $defeatTeamStats->addLost();
                $defeatTeamStatsAll->addLost();
            }

        } elseif ($action == Statistics::ACTION_REMOVE) {

            if ($isDraw) {
                $firstTeamStats->removeDrawn();
                $secondTeamStats->removeDrawn();
                $firstTeamStatsAll->removeDrawn();
                $secondTeamStatsAll->removeDrawn();
                // Update statistic streak
                $statisticsService->updateStreak($firstTeamStats);
                $statisticsService->updateStreak($firstTeamStatsAll);
                $statisticsService->updateStreak($secondTeamStats);
                $statisticsService->updateStreak($secondTeamStatsAll);
            } else {
                $winnerTeamStats->removeWon();
                $winnerTeamStatsAll->removeWon();
                $defeatTeamStats->removeLost();
                $defeatTeamStatsAll->removeLost();
                // Update statistic streak
                $statisticsService->updateStreak($winnerTeamStats);
                $statisticsService->updateStreak($winnerTeamStatsAll);
                $statisticsService->updateStreak($defeatTeamStats);
                $statisticsService->updateStreak($defeatTeamStatsAll);
            }

        }

        $this->entityManager->flush();
    }

    /**
     * Get team last streak
     * @param int $teamId
     * @param null $month
     * @param null $year
     * @param int $limit
     * @return array
     */
    public function getStreak($teamId, $month = null, $year = null, $limit = Statistics::STREAK_COUNT)
    {
        $team = $this->entityManager
            ->getRepository('AppBundle:Team')
            ->findBy(array('id' => $teamId));

        $startDate = '2016-01-01';
        $endDate = 'now';

        if ($month && $year) {
            $startDate = $year.'-'.$month;
            $endDate = date("Y-m-t", strtotime($startDate));
        }

        $games = $this->entityManager
            ->getRepository('AppBundle:Game')
            ->getGamesByDate(
                new \DateTime($startDate),
                new \DateTime($endDate),
                $team,
                null,
                $limit
            )
            ->getResult();

        $streak = null;

        if ($games) {
            $streak = array();
            /** @var GameEntity $game */
            foreach ($games as $game) {
                if ($game->getResult() == 0) {
                    $streak[] = 'drawn';
                } else if ($game->getResult() == 1) {
                    $streak[] = $game->getFirstTeam()->getId() === $teamId ? 'won' : 'lost';
                } elseif ($game->getResult() == 2) {
                    $streak[] = $game->getSecondTeam()->getId() === $teamId ? 'won' : 'lost';
                }
            }
        }

        return $streak;
    }
}

<?php

namespace AppBundle\Service;

use AppBundle\Entity\Game as GameEntity;
use AppBundle\Entity\Statistics;
use Doctrine\ORM\EntityManager;

class Team
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * @param array $teamMembers array of team members
     * @return Team|array
     */
    public function findTeam($teamMembers)
    {
        $team = $this->entityManager->getRepository('AppBundle:Team')->findTeamByMembers($teamMembers);

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
        $isDraw = false;
        $winnerTeamStats = null;
        $defeatTeamStats = null;
        $winnerTeamStatsAll = null;
        $defeatTeamStatsAll = null;
        $date = $game->getGameDate();
        $firstTeamStats = $statsRepository
            ->getStatistic(
                $game->getFirstTeam(),
                (int)$date->format('m'),
                (int)$date->format('Y')
            );
        $firstTeamStatsAll = $statsRepository->getStatistic($game->getFirstTeam());
        $secondTeamStats = $statsRepository
            ->getStatistic(
                $game->getSecondTeam(),
                (int)$date->format('m'),
                (int)$date->format('Y')
            );
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
            } else {
                $winnerTeamStats->removeWon();
                $winnerTeamStatsAll->removeWon();
                $defeatTeamStats->removeLost();
                $defeatTeamStatsAll->removeLost();
            }

        }

        $this->entityManager->flush();
    }

    /**
     * Get team last streak
     * @param int $teamId
     * @param int $limit
     * @return array
     */
    public function getStreak($teamId, $limit = 5)
    {
        $team = $this->entityManager
            ->getRepository('AppBundle:Team')
            ->findBy(array('id' => $teamId));

        $games = $this->entityManager
            ->getRepository('AppBundle:Game')
            ->getGamesByDate(
                new \DateTime('2016-01-01'),
                new \DateTime('now'),
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

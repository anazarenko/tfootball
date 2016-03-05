<?php

namespace AppBundle\Service;

use AppBundle\Entity\Game;
use AppBundle\Entity\Statistics;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class Team
{
    protected $templating;
    protected $entityManager;

    public function __construct(EntityManager $entityManager, EngineInterface $templating)
    {
        $this->entityManager = $entityManager;
        $this->templating = $templating;
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

            $this->entityManager->persist($statistics);
            $this->entityManager->flush();

            $team->setStatistics($statistics);
            $this->entityManager->flush();
        }

        return $team;

    }

    /**
     * Update team statistics if game is confirmed
     *
     * @param Game $game
     * @param int $action
     */
    public function updateStatistics(Game $game, $action = Statistics::ACTION_ADD)
    {
        $winnerTeam = null;
        $defeatTeam = null;
        $isDraw = false;
        $firstTeam = $game->getFirstTeam();
        $secondTeam = $game->getSecondTeam();

        if ($game->getResult() == Game::RESULT_DRAW) {
            $isDraw = true;
        } else {
            if ($game->getResult() == Game::RESULT_FIRST_WINNER) {
                $winnerTeam = $firstTeam;
                $defeatTeam = $secondTeam;
            } else {
                $winnerTeam = $secondTeam;
                $defeatTeam = $firstTeam;
            }
        }

        if ($action == Statistics::ACTION_ADD) {

            if ($isDraw) {
                $firstTeam->getStatistics()->addDrawn();
                $secondTeam->getStatistics()->addDrawn();
            } else {
                $winnerTeam->getStatistics()->addWon();
                $defeatTeam->getStatistics()->addLost();
            }

        } elseif ($action == Statistics::ACTION_REMOVE) {

            if ($isDraw) {
                $firstTeam->getStatistics()->removeDrawn();
                $secondTeam->getStatistics()->removeDrawn();
            } else {
                $winnerTeam->getStatistics()->removeWon();
                $defeatTeam->getStatistics()->removeLost();
            }

        }

        $this->entityManager->flush();
    }
}

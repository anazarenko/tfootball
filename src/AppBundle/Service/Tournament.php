<?php

namespace AppBundle\Service;

use AppBundle\Entity\Confirm;
use AppBundle\Entity\Game as GameEntity;
use AppBundle\Entity\Tournament as TournamentEntity;
use AppBundle\Entity\Team as TeamEntity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Tournament
{
    protected $entityManager;
    protected $container;

    public function __construct(EntityManager $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * @param GameEntity $game
     */
    public function acceptGame(GameEntity $game)
    {
        foreach ($game->getConfirms() as $confirm) {
            $confirm->setStatus(Confirm::STATUS_CONFIRMED);
        }

        $this->container->get('app.team_service')->updateStatistics($game);

        $game->setStatus(GameEntity::STATUS_CONFIRMED);
        $this->entityManager->flush();

        if ($game->getStage() === GameEntity::STAGE_GROUP) {
            $this->updateTournamentStatistics($game);
        }
    }

    /**
     * @param GameEntity $game
     */
    public function updateTournamentStatistics(GameEntity $game)
    {
        $stat = $this->parseGameToStat($game->getTournament(), $game->getFirstTeam());
        $firstTeamStat = $this->getTournamentTeamStat($game->getTournament(), $game->getFirstTeam());
        $firstTeamStat->setWon($stat['won']);
        $firstTeamStat->setDrawn($stat['drawn']);
        $firstTeamStat->setLost($stat['lost']);

        $stat = $this->parseGameToStat($game->getTournament(), $game->getSecondTeam());
        $secondTeamStat = $this->getTournamentTeamStat($game->getTournament(), $game->getSecondTeam());
        $secondTeamStat->setWon($stat['won']);
        $secondTeamStat->setDrawn($stat['drawn']);
        $secondTeamStat->setLost($stat['lost']);

        $this->entityManager->flush();
    }

    /**
     * @param TournamentEntity $tournament
     * @param TeamEntity $team
     * @return \AppBundle\Entity\TournamentStatistics|null|object
     */
    public function getTournamentTeamStat(TournamentEntity $tournament, TeamEntity $team)
    {
        return $this->entityManager
            ->getRepository('AppBundle:TournamentStatistics')
            ->findOneBy(
                array(
                    'tournament' => $tournament->getId(),
                    'team' => $team->getId()
                )
            );
    }

    /**
     * @param TournamentEntity $tournament
     * @param TeamEntity $team
     * @return array
     */
    public function getTournamentTeamGames(TournamentEntity $tournament, TeamEntity $team)
    {
        $queryBuilder = $this->entityManager
            ->getRepository('AppBundle:Game')
            ->createQueryBuilder('g');

        return $queryBuilder
            ->where('g.tournament = :tournament')
            ->andWhere('g.status != :status')
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('g.firstTeam', $team->getId()),
                    $queryBuilder->expr()->eq('g.secondTeam', $team->getId())
                )
            )
            ->setParameter('tournament', $tournament->getId())
            ->setParameter('status', GameEntity::STATUS_NEW)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param TournamentEntity $tournament
     * @param TeamEntity $team
     * @return array
     */
    public function parseGameToStat(TournamentEntity $tournament, TeamEntity $team)
    {
        $stat = array(
            'won' => 0,
            'lost' => 0,
            'drawn' => 0
        );

        /** @var GameEntity $game */
        foreach ($this->getTournamentTeamGames($tournament, $team) as $game) {
            switch ($game->getResult()) {
                case GameEntity::RESULT_DRAW :
                    $stat['drawn']++;
                    break;
                case GameEntity::RESULT_FIRST_WINNER :
                    if ($game->getFirstTeam() === $team) {
                        $stat['won']++;
                    } else {
                        $stat['lost']++;
                    }
                    break;
                case GameEntity::RESULT_SECOND_WINNER :
                    if ($game->getSecondTeam() === $team) {
                        $stat['won']++;
                    } else {
                        $stat['lost']++;
                    }
                    break;
            }
        }

        return $stat;
    }

}

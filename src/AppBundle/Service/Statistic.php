<?php

namespace AppBundle\Service;

use AppBundle\Entity\Game as GameEntity;
use AppBundle\Entity\Statistics;
use AppBundle\Entity\Team as TeamEntity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Statistic
{
    protected $entityManager;
    protected $container;

    public function __construct(EntityManager $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * Update streak in statistics item
     * @param Statistics $statistics
     */
    public function updateStreak(Statistics & $statistics)
    {
        $teamService = $this->container->get('app.team_service');

        $streak = $teamService
            ->getStreak($statistics->getId(), $statistics->getMonth(), $statistics->getYear());

        $statistics->setStreak($streak);
    }

    /**
     * Get team streak for twig extension
     * @param TeamEntity | int $team
     * @param int $count
     * @return array
     */
    public function getStreak($team, $count = Statistics::STREAK_COUNT)
    {
        if (is_int($team)) {
            $team = $this->entityManager->getRepository('AppBundle:Team')->findOneBy(array('id' => $team));
        }
        $gameService = $this->container->get('app.game_service');
        $streak = array();

        $queryBuilder = $this->entityManager
            ->getRepository('AppBundle:Game')
            ->createQueryBuilder('g');

        $games = $queryBuilder
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('g.firstTeam', $team->getId()),
                    $queryBuilder->expr()->eq('g.secondTeam', $team->getId())
                )
            )
            ->andWhere('g.status = ' . GameEntity::STATUS_CONFIRMED)
            ->orderBy('g.gameDate', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();

        /** @var GameEntity $game */
        foreach ($games as $game) {
            switch ($gameService->getTeamGameResult($game, $team)) {
                case -1 :
                    $streak[] = 'lost';
                    break;
                case 0 :
                    $streak[] = 'drawn';
                    break;
                case 1 :
                    $streak[] = 'won';
                    break;
            }
        }

        return $streak;
    }
}


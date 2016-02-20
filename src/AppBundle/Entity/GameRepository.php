<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * GameRepository
 */
class GameRepository extends EntityRepository
{
    /**
     * @param \AppBundle\Entity\User $user
     * @return array
     */
    public function getNotify($user) {

        $qb = $this->createQueryBuilder('g');
        $games = $qb->select('g')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('g.firstPlayer', ':user'),
                        $qb->expr()->eq('g.confirmedFirst', 0)
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->eq('g.secondPlayer', ':user'),
                        $qb->expr()->eq('g.confirmedSecond', 0)
                    )
                )
            )
            ->orderBy('g.gameDate', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $games;
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param null $firstTeam
     * @param null $secondTeam
     * @return \Doctrine\ORM\Query
     */
    public function getGamesByDate(\DateTime $startDate, \DateTime $endDate, $firstTeam = null, $secondTeam = null) {

        $qb = $this->createQueryBuilder('g');
        $gamesQuery = $qb->select('g')
            ->where('g.status = :status')
            ->andWhere($qb->expr()->between('g.gameDate', ':start', ':end'))
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate->modify('+ 1 day'))
            ->setParameter('status', Game::STATUS_CONFIRMED);

        if ($firstTeam && $secondTeam) {
            $gamesQuery->andWhere($qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->eq('g.firstTeam', ':firstTeam'),
                    $qb->expr()->eq('g.secondTeam', ':secondTeam')
                ),
                $qb->expr()->andX(
                    $qb->expr()->eq('g.firstTeam', ':secondTeam'),
                    $qb->expr()->eq('g.secondTeam', ':firstTeam')
                )
            ))->setParameter('firstTeam', $firstTeam)->setParameter('secondTeam', $secondTeam);
        } elseif ($firstTeam) {
            $gamesQuery->andWhere($qb->expr()->orX(
                $qb->expr()->eq('g.firstTeam', ':firstTeam'),
                $qb->expr()->eq('g.secondTeam', ':firstTeam')
            ))->setParameter('firstTeam', $firstTeam);
        } elseif ($secondTeam) {
            $gamesQuery->andWhere($qb->expr()->orX(
                $qb->expr()->eq('g.firstTeam', ':secondTeam'),
                $qb->expr()->eq('g.secondTeam', ':secondTeam')
            ))->setParameter('secondTeam', $secondTeam);
        }

        $gamesQuery->orderBy('g.gameDate', 'DESC');

        return $gamesQuery->getQuery();
    }

    public function getSingleGames($user) {

        $qb = $this->createQueryBuilder('g');
        $games = $qb->select('g')
            ->where('g.form = 0')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('g.firstPlayer', ':user'),
                    $qb->expr()->eq('g.secondPlayer', ':user')
                )
            )
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $games;
    }

    public function getUserWonGames($user) {

        $qb = $this->createQueryBuilder('g');
        $games = $qb->select('g')
            ->where('g.winner = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $games;
    }
}

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
     * @return array
     */
    public function getGamesByDate(\DateTime $startDate, \DateTime $endDate) {
        $qb = $this->createQueryBuilder('g');
        $gamesQuery = $qb->select('g')
            ->where('g.status = :status')
            ->andWhere($qb->expr()->between('g.gameDate', ':start', ':end'))
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('status', Game::STATUS_CONFIRMED)
            ->orderBy('g.gameDate', 'DESC')
            ->getQuery();

        return $gamesQuery;
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

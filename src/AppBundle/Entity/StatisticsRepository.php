<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * StatisticsRepository
 *
 */
class StatisticsRepository extends EntityRepository
{
    /**
     * @param Team $team
     * @param int $month
     * @param int $year
     * @return Statistics|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getStatistic(Team $team, $month = 0, $year = 0)
    {
        $qb = $this->createQueryBuilder('stat')
            ->where('stat.team = :team')
            ->andWhere('stat.month = :month')
            ->andWhere('stat.year = :year')
            ->setParameter('team', $team->getId())
            ->setParameter('month', $month)
            ->setParameter('year', $year);

        $statistics = $qb->getQuery()->getOneOrNullResult();

        if (!$statistics) {
            $statistics = new Statistics();
            $statistics->setTeam($team);
            $statistics->setMonth($month);
            $statistics->setYear($year);
            $this->getEntityManager()->persist($statistics);
            $this->getEntityManager()->flush();
        }

        return $statistics;
    }

    /**
     * @param int $count
     * @return \Doctrine\ORM\Query
     */
    public function getStatisticsByTeamCount($count = 1)
    {
        $qb = $this->createQueryBuilder('stat')
            ->select(array('stat', 'team'))
            ->join('stat.team', 'team')
            ->where('team.playerCount = :count')
            ->andWhere('stat.month = 0')
            ->andWhere('stat.year = 0')
            ->orderBy('stat.wonPercentage', 'DESC')
            ->setParameter('count', $count);

        return $qb->getQuery();
    }

    /**
     * @param Team $firstTeam
     * @param Team $secondTeam
     * @return \Doctrine\ORM\Query
     */
    public function getStatH2H(Team $firstTeam, Team $secondTeam)
    {
        $qb = $this->createQueryBuilder('stat');

        $statQuery = $qb->select(array('stat', 'team'))
            ->join('stat.team', 'team')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('stat.team', $firstTeam->getId()),
                    $qb->expr()->eq('stat.team', $secondTeam->getId())
                )
            )
            ->andWhere('stat.month = 0')
            ->andWhere('stat.year = 0')
            ->orderBy('stat.wonPercentage', 'DESC');

        return $statQuery->getQuery();
    }
}

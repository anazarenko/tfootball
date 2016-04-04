<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class StatisticsController extends Controller
{
    /**
     * @Route("/statistics", name="_statistics_page")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $startDate = new \DateTime($this->getParameter('app.start_date'));
        $endDate = new \DateTime('now');
        $statList = array();

        // Get Statistic repository
        $statRepository = $this->getDoctrine()->getRepository('AppBundle:Statistics');

        while ($startDate <= $endDate) {
            $listItem = array(
                'month' => $startDate->format('M'),
                'year' => $startDate->format('Y')
            );

            $listItem['singleStat'] = $statRepository->createQueryBuilder('stat')
                ->select(array('stat', 'team'))
                ->join('stat.team', 'team')
                ->where('stat.month = :month')
                ->andWhere('stat.year = :year')
                ->andWhere('stat.month = :month')
                ->andWhere('team.playerCount = 1')
                ->orderBy('stat.wonPercentage', 'DESC')
//                ->setMaxResults(1)
                ->setParameter('month', (int)$startDate->format('m'))
                ->setParameter('year', (int)$startDate->format('Y'))
                ->getQuery()
                ->getArrayResult();

            $listItem['doubleStat'] = $statRepository->createQueryBuilder('stat')
                ->select(array('stat', 'team'))
                ->join('stat.team', 'team')
                ->where('stat.month = :month')
                ->andWhere('stat.year = :year')
                ->andWhere('stat.month = :month')
                ->andWhere('team.playerCount = 2')
                ->orderBy('stat.wonPercentage', 'DESC')
//                ->setMaxResults(1)
                ->setParameter('month', (int)$startDate->format('m'))
                ->setParameter('year', (int)$startDate->format('Y'))
                ->getQuery()
                ->getArrayResult();

            $statList[] = $listItem;
            $startDate->modify('+ 1 month');
        }

        return $this->render(
            'AppBundle:Statistics:index.html.twig',
            array(
                'active' => 'stats',
                'statList' => $statList
            )
        );
    }
}

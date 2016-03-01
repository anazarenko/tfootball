<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Confirm;
use AppBundle\Entity\Game;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Form\GameCreateType;
use AppBundle\Form\GameFilterType;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/h2h")
 */
class HeadToHeadController extends Controller
{
    /**
     * @Route("/", name="_head_to_head")
     * @param Request $request
     * @return Response
     */
    public function matchAction(Request $request)
    {
        // Default date range
        $dateRange = $this->getParameter('app.start_date').'-'.date('d.m.Y', time());
        $dates = explode('-', $dateRange);

        // Get team repository
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');

        // Get game repository
        $gameRepository = $games = $this->getDoctrine()->getRepository('AppBundle:Game');

        // Get page for pagination
        $page = (!empty($request->request->getInt('page'))) ? $request->request->getInt('page') : 1;

        // If ajax POST then get teams param
        $filterFirstTeam = $request->request->get('firstTeam') ? $request->request->get('firstTeam') : null;
        $filterSecondTeam = $request->request->get('secondTeam') ? $request->request->get('secondTeam') : null;

        // Create Game filter for teams
        $game = new Game();
        $form = $this->createForm(
            new GameFilterType(),
            $game,
            array(
                'action' => $this->generateUrl('_head_to_head'),
                'method' => 'POST'
            )
        );

        $form->handleRequest($request);

        // If sent filter form
        if ($form->isSubmitted() && $form->isValid()) {

            // Get date range
            if ($request->request->get('dateRange')) {
                $dateRange = $request->request->get('dateRange');
                $dates = explode('-', $dateRange);
            }

            // Get array with players
            $data = $request->request->get('game_filter');

            $filterFirstTeam = isset($data['firstTeam']) ? $data['firstTeam'] : null;
            $filterSecondTeam = isset($data['secondTeam']) ? $data['secondTeam'] : null;
        }

        // Check for valid if sent two teams
        if ($filterFirstTeam &&
            $filterSecondTeam &&
            !$this->get('app.team_service')->isValidTeams($filterFirstTeam, $filterSecondTeam, $errorMsg))
        {
            $form->addError(new FormError($errorMsg));
            return $this->render(
                'AppBundle:HeadToHead:index.html.twig',
                array(
                    'active' => 'h2h',
                    'pagination' => '',
                    'form' => $form->createView(),
                    'moreBtn' => false,
                    'startDate' => $dates[0],
                    'endDate' => $dates[1]
                )
            );

        }

        // Get Teams
        $firstTeam = $teamRepository->findTeamByMemberIDs($filterFirstTeam);
        $secondTeam = $teamRepository->findTeamByMemberIDs($filterSecondTeam);

        if (!$firstTeam || !$secondTeam) {
            return $this->render(
                'AppBundle:HeadToHead:index.html.twig',
                array(
                    'active' => 'games',
                    'pagination' => '',
                    'form' => $form->createView(),
                    'moreBtn' => false,
                    'startDate' => $dates[0],
                    'endDate' => $dates[1]
                )
            );
        }

        // Get games query
        $gamesQuery = $gameRepository
            ->getGamesByDate(
                new \DateTime($dates[0]),
                new \DateTime($dates[1]),
                $teamRepository->findTeamByMemberIDs($filterFirstTeam),
                $teamRepository->findTeamByMemberIDs($filterSecondTeam)
            );

        // Get games query
        $gamesStatsQuery = $gameRepository
            ->getGamesByDate(
                new \DateTime($dates[0]),
                new \DateTime($dates[1]),
                $teamRepository->findTeamByMemberIDs($filterFirstTeam),
                $teamRepository->findTeamByMemberIDs($filterSecondTeam)
            );

        // Get array of sorting matches for team
        $teamStats = $this->get('app.game_service')->parseGamesByPlayers($gamesStatsQuery->getResult());

        $firstTeamStats = array_pop($teamStats);
        $secondTeamStats = array_pop($teamStats);

        // Create pagination
        $paginator  = $this->get('knp_paginator');

        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $gamesQuery, /* query NOT result */
            $page, /* page number */
            $this->container->getParameter('game.limit_per_page') /* limit per page */
        );

        // More btn
        $moreBtn = ($page >= $pagination->getPageCount()) ? false : true;

        // If async request
        if ($request->isXmlHttpRequest()) {

            $games = $this->renderView('AppBundle:HeadToHead:item.html.twig', array('games' => $pagination));
            $data = array('status' => 1, 'moreBtn' => $moreBtn, 'games' => $games, 'page' => $page + 1);

            $json = json_encode($data);
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return $this->render(
            'AppBundle:HeadToHead:index.html.twig',
            array(
                'active' => 'h2h',
                'pagination' => $pagination,
                'form' => $form->createView(),
                'teamStats' => $teamStats,
                'firstTeamStats' => $firstTeamStats,
                'secondTeamStats' => $secondTeamStats,
                'moreBtn' => $moreBtn,
                'startDate' => $dates[0],
                'endDate' => $dates[1]
            )
        );
    }
}

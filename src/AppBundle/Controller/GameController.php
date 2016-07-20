<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Confirm;
use AppBundle\Entity\Game;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Form\GameCreateType;
use AppBundle\Form\GameFilterType;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * @Route("/", name="_games")
     * @param Request $request
     * @return Response
     */
    public function matchAction(Request $request)
    {
        // Default date range
        $dateRange = $this->getParameter('app.start_date').'-'.date('d.m.Y', time());
        $dates = explode('-', $dateRange);

        // Get game repository
        $gameRepository = $this->getDoctrine()->getRepository('AppBundle:Game');

        // Get Statistic repository
        $statRepository = $this->getDoctrine()->getRepository('AppBundle:Statistics');

        // Get page for pagination
        $page = (!empty($request->request->getInt('page'))) ? $request->request->getInt('page') : 1;

        // Get games query
        $gamesQuery = $gameRepository->getGamesByDate(new \DateTime($dates[0]), new \DateTime($dates[1]));

        // Get Stats
        $singlePlayerStatistics = $statRepository->getStatisticsByTeamCount(1)->getArrayResult();
        $multiPlayerStatistics = $statRepository->getStatisticsByTeamCount(2)->getArrayResult();

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

            $games = $this->renderView('AppBundle:Game:item.html.twig', array('games' => $pagination));
            $data = array('status' => 1, 'moreBtn' => $moreBtn, 'games' => $games, 'page' => $page + 1);

            $json = json_encode($data);
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return $this->render(
            'AppBundle:Game:index.html.twig',
            array(
                'active' => 'games',
                'pagination' => $pagination,
                'moreBtn' => $moreBtn,
                'singlePlayerStatistics' => $singlePlayerStatistics,
                'multiPlayerStatistics' => $multiPlayerStatistics
            )
        );
    }

    /**
     * @Route("/create", name="_game_create")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gameCreateAction(Request $request)
    {
        $game = new Game();
        $form = $this->createForm(
            new GameCreateType(),
            $game,
            array(
                'action' => $this->generateUrl('_game_create'),
                'method' => 'POST'
            )
        );

        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {
                $response = $this->get('app.game_service')
                    ->createGame($game, $this->getUser(), $request->request->get('game_create'));
            } else {
                $response = array('status' => 0, 'error' => 'Oops. Something went wrong. Please try again.');
            }

            $json = json_encode($response);
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return $this->redirectToRoute('_games');
    }

    /**
     * @Route("/{id}/accept", name="_game_accept")
     * @Method("POST")
     *
     * @param Request $request
     * @param Game $game
     * @return Response
     */
    public function gameAcceptAction(Request $request, Game $game)
    {
        if ($request->isXmlHttpRequest()) {
            $response = $this->get('app.game_service')->acceptGame($game, $this->getUser());
            $json = json_encode($response);

            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        throw new NotFoundHttpException('Page not found');
    }

    /**
     * @Route("/{id}/decline", name="_game_decline")
     * @Method("POST")
     *
     * @param Request $request
     * @param Game $game
     * @return Response
     */
    public function gameDeclineAction(Request $request, Game $game)
    {
        if ($request->isXmlHttpRequest()) {
            $response = $this->get('app.game_service')->declineGame($game, $this->getUser());
            $json = json_encode($response);
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        throw new NotFoundHttpException('Page not found');
    }
}

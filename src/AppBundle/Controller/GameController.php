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

        // Get User repository
        $statRepository = $this->getDoctrine()->getRepository('AppBundle:Statistics');

        // Get page for pagination
        $page = (!empty($request->request->getInt('page'))) ? $request->request->getInt('page') : 1;

        // Get games query
        $gamesQuery = $gameRepository->getGamesByDate(new \DateTime($dates[0]), new \DateTime($dates[1]));

        // Get Stats
        $singlePlayerStatistics = $statRepository->createQueryBuilder('stat')
            ->select(array('stat', 'team'))
            ->join('stat.team', 'team')
            ->where('team.playerCount = 1')
            ->orderBy('stat.wonPercentage', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $multiPlayerStatistics = $statRepository->createQueryBuilder('stat')
            ->select(array('stat', 'team'))
            ->join('stat.team', 'team')
            ->where('team.playerCount > 1')
            ->orderBy('stat.wonPercentage', 'DESC')
            ->getQuery()
            ->getArrayResult();

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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gameCreateAction(Request $request)
    {
        $game = new Game();
        $referer = $request->headers->get('referer');
        $form = $this->createForm(
            new GameCreateType(),
            $game,
            array(
                'action' => $this->generateUrl('_game_create'),
                'method' => 'POST'
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $request->request->get('game_create');
            $userRepo = $this->getDoctrine()->getRepository('AppBundle:User');
            $entityManager = $this->getDoctrine()->getManager();
            $errorMsg = '';

            if (empty($game->getFirstScore()) || empty($game->getSecondScore())) {
                if ($request->isXmlHttpRequest()) {

                    $data = array('status' => 0, 'error' => 'Incorrect data');

                    $json = json_encode($data);
                    $response = new Response($json, 200);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

                return new RedirectResponse($referer);
            }

            if (!$this->get('app.team_service')->isValidTeams($data['firstTeam'], $data['secondTeam'], $errorMsg)) {
                if ($request->isXmlHttpRequest()) {

                    $data = array('status' => 0, 'error' => $errorMsg);

                    $json = json_encode($data);
                    $response = new Response($json, 200);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

                return new RedirectResponse($referer);
            }

            /** @var \AppBundle\Entity\User $user */
            $user = $this->getUser();

            $firstTeamEntitiesArray = array();
            $secondTeamEntitiesArray = array();
            $userEntitiesArray = array();

            foreach($data['firstTeam'] as $userID) {
                // Get user in team
                $currentUser = $userRepo->findOneBy(array('id' => $userID));
                // Add user entity to team array
                $firstTeamEntitiesArray[] = $currentUser;
                // Add user entity to users array
                $userEntitiesArray[] = $currentUser;
                // Add current user to game
                $game->addPlayer($currentUser);
            }

            foreach($data['secondTeam'] as $userID) {
                // Get user in team
                $currentUser = $userRepo->findOneBy(array('id' => $userID));
                // Add user entity to team array
                $secondTeamEntitiesArray[] = $currentUser;
                // Add user entity to users array
                $userEntitiesArray[] = $currentUser;
                // Add current user to game
                $game->addPlayer($currentUser);
            }

            $firstTeam = $this->get('app.team_service')->findTeam($firstTeamEntitiesArray);
            $secondTeam = $this->get('app.team_service')->findTeam($secondTeamEntitiesArray);

            $game->setFirstTeam($firstTeam);
            $game->setSecondTeam($secondTeam);
            $game->setType(Game::TYPE_FRIENDLY);
            $game->setCreator($user);
            $game->setDifference(abs($game->getFirstScore() - $game->getSecondScore()));

            if ($game->getFirstScore() > $game->getSecondScore()) {
                $game->setResult(Game::RESULT_FIRST_WINNER);
            } elseif ($game->getFirstScore() < $game->getSecondScore()) {
                $game->setResult($game::RESULT_SECOND_WINNER);
            } elseif ($game->getFirstScore() == $game->getSecondScore()) {
                $game->setResult($game::RESULT_DRAW);
            }

            $game->setStatus($game::STATUS_NEW);

            $entityManager->persist($game);
            $entityManager->flush();

            /** @var \AppBundle\Entity\User $currentUser */
            foreach ($userEntitiesArray as $currentUser) {
                $currentUser->addGame($game);
                // Create new confirm entity
                $confirm = new Confirm();
                $confirm->setGame($game);
                $confirm->setUser($currentUser);
                $confirm->setStatus($currentUser == $user ? Confirm::STATUS_CONFIRMED : Confirm::STATUS_NEW);

                $entityManager->persist($confirm);
                $entityManager->persist($currentUser);
            }

            $entityManager->persist($game);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {

                $data = array('status' => 1, 'error' => 'New Game was added!');

                $json = json_encode($data);
                $response = new Response($json, 200);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

            $this->addFlash('success', 'New Game was added!');

        } else {
            if ($request->isXmlHttpRequest()) {

                $data = array('status' => 0, 'error' => 'Oops. Something went wrong. Please try again.');

                $json = json_encode($data);
                $response = new Response($json, 200);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $this->addFlash('error', 'Oops. Something went wrong. Please try again.');
            return new RedirectResponse($referer);

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

            $data = array('status' => 0);

            /** @var \AppBundle\Entity\User $user */
            $user = $this->getUser();

            $confirmRepo = $this->getDoctrine()->getRepository('AppBundle:Confirm');
            $confirm = $confirmRepo->findOneBy(array('user' => $user, 'game' => $game));

            if ($confirm) {
                $confirm->setStatus(Confirm::STATUS_CONFIRMED);

                if ($game->getStatus() != Game::STATUS_REJECTED) {
                    $completeGame = true;
                    /** @var \AppBundle\Entity\Confirm $currentConfirm */
                    foreach ($game->getConfirms() as $currentConfirm) {
                        if ($currentConfirm->getStatus() != Confirm::STATUS_CONFIRMED) {
                            $completeGame = false;
                            break;
                        }
                    }

                    if ($completeGame) {
                        $game->setStatus(Game::STATUS_CONFIRMED);
                        $this->get('app.team_service')->updateStatistics($game);
                    }
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $data = array('status' => 1);
            }
            $json = json_encode($data);
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

            $data = array('status' => 0);

            /** @var \AppBundle\Entity\User $user */
            $user = $this->getUser();

            $confirmRepo = $this->getDoctrine()->getRepository('AppBundle:Confirm');
            $confirm = $confirmRepo->findOneBy(array('user' => $user, 'game' => $game));

            if ($confirm) {
                $confirm->setStatus(Confirm::STATUS_REJECTED);

                $game->setStatus(Game::STATUS_REJECTED);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $data = array('status' => 1);
            }
            $json = json_encode($data);
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        throw new NotFoundHttpException('Page not found');
    }
}

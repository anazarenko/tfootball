<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Confirm;
use AppBundle\Entity\Game;
use AppBundle\Entity\Statistics;
use AppBundle\Entity\Team;
use AppBundle\Entity\Tournament;
use AppBundle\Entity\TournamentStatistics;
use AppBundle\Form\TournamentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/tournament")
 */
class TournamentController extends Controller
{
    /**
     * @Route("/", name="_tournaments_list")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $tournaments = $this->getDoctrine()->getRepository('AppBundle:Tournament')->findAll();

        return $this->render(
            'AppBundle:Tournament:index.html.twig',
            array(
                'active' => 'tournaments',
                'tournaments' => $tournaments
            )
        );
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="_tournaments_page")
     *
     * @param Request $request
     * @param Tournament $tournament
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tournamentAction(Request $request, Tournament $tournament)
    {
        $statistics = $this->getDoctrine()
            ->getRepository('AppBundle:TournamentStatistics')
            ->findBy(array('tournament' => $tournament->getId()), array('position' => 'asc', 'points' => 'desc'));

        $games = $this->getDoctrine()
            ->getRepository('AppBundle:Game')
            ->findBy(array('tournament' => $tournament->getId(), 'stage' => Game::STAGE_GROUP));

        $playoffGames = $this->getDoctrine()
            ->getRepository('AppBundle:Game')
            ->createQueryBuilder('g')
            ->where('g.tournament = :tournament')
            ->andWhere('g.stage != 0')
            ->setParameter('tournament', $tournament->getId())
            ->getQuery()
            ->getResult();

        $stages = $this->getDoctrine()
            ->getRepository('AppBundle:Game')
            ->createQueryBuilder('g')
            ->select('g.stage as id')
            ->where('g.tournament = :tournament')
            ->andWhere('g.stage != 0')
            ->setParameter('tournament', $tournament->getId())
            ->groupBy('g.stage')
            ->getQuery()
            ->getResult();

        return $this->render(
            'AppBundle:Tournament:tournament.html.twig',
            array(
                'active' => 'tournaments',
                'statistics' => $statistics,
                'games' => $games,
                'tournament' => $tournament,
                'playoffGames' => $playoffGames,
                'stages' => array_reverse($stages),
                'availableStages' => Game::$availableStages
            )
        );
    }

    /**
     * @Route("/create", name="_tournaments_create")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $tournament = new Tournament();

        $form = $this->createForm(TournamentType::class, $tournament);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teams = $request->request->get('team');

            $response = $this->validateTeams($teams, $tournament);

            if (!$response['status']) {
                $form->addError(new FormError($response['errorMsg']));
            } else {
                $tournament->setCreator($this->getUser());
                $teams = $response['data'];

                /** @var Team $team */
                foreach ($teams as $team) {
                    $team->addTournament($tournament);
                    $tournament->addTeam($team);
                }

                $this->getDoctrine()->getManager()->flush();
                $this->createGames($teams, $tournament);
                $this->createStatistics($teams, $tournament);

                return $this->redirectToRoute('_tournaments_list');
            }
        }

        return $this->render(
            'AppBundle:Tournament:create.html.twig',
            array('active' => 'tournaments', 'form' => $form->createView())
        );
    }

    /**
     * @Route("/game/accept/{id}", requirements={"id" = "\d+"}, name="_tournaments_game_accept")
     * @Method("POST")
     * @param Request $request
     * @param Game $game
     * @return JsonResponse
     */
    public function acceptGame(Request $request, Game $game)
    {
        $response = array('status' => 1);

        $firstScore = (int)$request->request->get('firstScore');
        $secondScore = (int)$request->request->get('secondScore');

        if ($firstScore === null || $secondScore === null) {
            return new JsonResponse(array('status' => 0));
        }

        $game->setFirstScore($firstScore);
        $game->setSecondScore($secondScore);
        $game->setGameDate(new \DateTime('now'));
        $game->setDifference(abs($firstScore - $secondScore));

        if ($firstScore > $secondScore) {
            $game->setResult(Game::RESULT_FIRST_WINNER);
            $game->setWinner($game->getFirstTeam());
            $game->setLoser($game->getSecondTeam());
        } elseif ($secondScore > $firstScore) {
            $game->setResult(Game::RESULT_SECOND_WINNER);
            $game->setWinner($game->getSecondTeam());
            $game->setLoser($game->getFirstTeam());
        } else {
            $game->setResult(Game::RESULT_DRAW);
        }

        // If edit game
        if ($game->getStatus() === Game::STATUS_CONFIRMED) {
            $this->get('app.team_service')->updateStatistics($game, Statistics::ACTION_REMOVE);
        }

        $this->get('app.tournament_service')->acceptGame($game);

        $statistics = $this->getDoctrine()
            ->getRepository('AppBundle:TournamentStatistics')
            ->findBy(array('tournament' => $game->getTournament()->getId()), array('points' => 'desc'));

        $games = $this->getDoctrine()
            ->getRepository('AppBundle:Game')
            ->findBy(array('tournament' => $game->getTournament()->getId(), 'stage' => Game::STAGE_GROUP));

        $response['statistics'] = $this->renderView(
            'AppBundle:Tournament:tbody.html.twig',
            array('statistics' => $statistics, 'tournament' => $game->getTournament())
        );

        $response['games'] = $this->renderView(
            'AppBundle:Game:tournamentItem.html.twig',
            array('games' => $games, 'tournament' => $game->getTournament())
        );

        return new JsonResponse($response);
    }

    /**
     * @Route("/playoff/generate/{id}", requirements={"id" = "\d+"}, name="_tournaments_playoff")
     * @Method("POST")
     * @param Request $request
     * @param Tournament $tournament
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function playoffAction(Request $request, Tournament $tournament)
    {
        if ($tournament->getCreator() == $this->getUser() || $this->isGranted('ROLE_ADMIN')) {
            $statisticRepository = $this->getDoctrine()->getRepository('AppBundle:TournamentStatistics');
            $positions = array_flip($request->request->get('position'));
            $playoffTeams = array();

            $tournament->setStatus(Tournament::STATUS_PLAYOFF);

            // Set team position
            foreach ($positions as $position => $statisticId) {
                $stat = $statisticRepository->findOneBy(array('id' => $statisticId));
                $stat->setPosition($position);
                $this->getDoctrine()->getManager()->flush();

                if ($position <= $tournament->getPlayoffTeamCount()) {
                    $playoffTeams[$position] = $stat->getTeam();
                }
            }

            if (count($playoffTeams) === $tournament->getPlayoffTeamCount()) {
                $tournament->setCurrentStage(intval(count($playoffTeams)/2));
                $this->getDoctrine()->getManager()->flush();

                $this->createPlayoffGames($playoffTeams, $tournament);
            }
        }

        return $this->redirectToRoute('_tournaments_page', array('id' => $tournament->getId()));
    }

    /**
     * @param array $teams
     * @param Tournament $tournament
     * @return array
     */
    protected function validateTeams($teams, Tournament $tournament)
    {
        $response = array('status' => true, 'errorMsg' => '', 'data' => '');
        $teamEntities = array();
        $playerIDs = array();

        if (count($teams) < $tournament->getPlayoffTeamCount()) {
            $response['errorMsg'] = 'Invalid recipient count.';
            $response['status'] = false;
            return $response;
        }

        foreach ($teams as $team) {
            $playerEntities = array();

            if (count($team) != $tournament->getForm()) {
                $response['errorMsg'] = 'Invalid recipient count';
                $response['status'] = false;
                return $response;
            }

            foreach ($team as $playerId) {
                if (in_array($playerId, $playerIDs)) {
                    $response['errorMsg'] = 'Duplicate user';
                    $response['status'] = false;
                    return $response;
                } else {
                    $playerEntities[] = $this->getDoctrine()
                        ->getRepository('AppBundle:User')
                        ->findOneBy(array('id' => $playerId));
                    $playerIDs[] = $playerId;
                }
            }

            $teamEntities[] = $this->get('app.team_service')->getTeam($playerEntities);
        }

        $response['data'] = $teamEntities;
        return $response;
    }

    /**
     * @param Team[] $teams
     * @param Tournament $tournament
     */
    protected function createGames($teams, Tournament $tournament)
    {
        $games = array();

        while (count($teams) > 1) {
            $currentTeam = array_pop($teams);

            foreach ($teams as $team) {
                for ($i = 1; $i <= $tournament->getRegularGameCount(); $i++) {
                    $game = new Game();
                    $game->setCreator($this->getUser());
                    $game->setStatus(Game::STATUS_NEW);
                    if ($i % 2 == 0) {
                        $game->setFirstTeam($currentTeam);
                        $game->setSecondTeam($team);
                    } else {
                        $game->setFirstTeam($team);
                        $game->setSecondTeam($currentTeam);
                    }
                    $game->setForm(Game::FORM_SINGLE);
                    $game->setStage(Game::STAGE_GROUP);
                    $game->setType(Game::TYPE_TOURNAMENT);
                    $game->setTournament($tournament);

                    $games[] = $game;
                }
            }
        }

        shuffle($games);

        foreach ($games as $game) {
            $this->getDoctrine()->getManager()->persist($game);
            $this->createConfirms($game);
        }

        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param Team[] $teams
     * @param Tournament $tournament
     */
    protected function createStatistics($teams, Tournament $tournament)
    {
        foreach ($teams as $team) {
            $statistic = new TournamentStatistics();
            $statistic->setTournament($tournament);
            $statistic->setTeam($team);

            $tournament->addStatistic($statistic);

            $this->getDoctrine()->getManager()->persist($statistic);
            $this->getDoctrine()->getManager()->flush();
        }
    }

    /**
     * Create confirms for game
     * @param Game $game
     */
    protected function createConfirms(Game $game)
    {
        // First team confirms
        foreach ($game->getFirstTeam()->getUsers() as $user) {
            $confirm = new Confirm();
            $confirm->setGame($game);
            $confirm->setUser($user);
            $confirm->setStatus(Confirm::STATUS_TOURNAMENT_NEW);
            $this->getDoctrine()->getManager()->persist($confirm);
        }

        // Second team confirms
        foreach ($game->getSecondTeam()->getUsers() as $user) {
            $confirm = new Confirm();
            $confirm->setGame($game);
            $confirm->setUser($user);
            $confirm->setStatus(Confirm::STATUS_TOURNAMENT_NEW);
            $this->getDoctrine()->getManager()->persist($confirm);
        }
    }

    /**
     * @param Team[] $teams
     * @param Tournament $tournament
     */
    protected function createPlayoffGames($teams, Tournament $tournament)
    {
        // Stage of matches ( 2 - 1/2, 4 - 1/4...)
        $stage = intval(count($teams)/2);
        // Array with correct games position
        $games = array();

        while (count($teams)) {
            $firstTeam = array_shift($teams);
            $secondTeam = array_pop($teams);
            $gameCount = count($games);

            $game = new Game();
            $game->setCreator($this->getUser());
            $game->setStatus(Game::STATUS_NEW);
            $game->setFirstTeam($firstTeam);
            $game->setSecondTeam($secondTeam);
            $game->setForm(Game::FORM_SINGLE);
            $game->setStage($stage);
            $game->setType(Game::TYPE_TOURNAMENT);
            $game->setTournament($tournament);

            if (count($games) % 2 === 0) {
                $games = array_merge(
                    array_slice($games, 0, intval($gameCount / 2), true),
                    array((intval($gameCount / 2) + 1) => $game),
                    array_slice($games, intval($gameCount / 2), $gameCount - 1, true)
                );
            } else {
                $beforeLength = intval(ceil($gameCount / 2) - 1) !== 0 ?: 1;
                $afterStart = intval(ceil($gameCount / 2)) - 1;

                $beforeGames = array_slice($games, 0, $beforeLength, true);
                $currentGame = array(intval(ceil($gameCount / 2)) => $game);
                $afterGames = array_slice($games, $afterStart, $gameCount - 1, true);

                $games = array_merge($beforeGames, $currentGame);

                foreach ($afterGames as $game) {
                    array_push($games, $game);
                }
            }
        }

        /** @var Game[] $games */
        foreach ($games as $game) {
            $this->getDoctrine()->getManager()->persist($game);
            $this->createConfirms($game);
        }

        $this->getDoctrine()->getManager()->flush();
        $this->createNextRounds($stage, $tournament);
    }

    protected function createNextRounds($currentStage, Tournament $tournament)
    {
        if ($currentStage !== Game::STAGE_FINAL) {
            do {
                $currentStage = $currentStage / 2;
                for ($i = 1; $i <= $currentStage; $i++) {
                    $game = new Game();
                    $game->setCreator($this->getUser());
                    $game->setStatus(Game::STATUS_NEW);
                    $game->setForm(Game::FORM_SINGLE);
                    $game->setStage($currentStage);
                    $game->setType(Game::TYPE_TOURNAMENT);
                    $game->setTournament($tournament);
                    $this->getDoctrine()->getManager()->persist($game);
                }
                $this->getDoctrine()->getManager()->flush();
            } while ($currentStage !== Game::STAGE_FINAL);
        }
    }

}

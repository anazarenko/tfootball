<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\Team;
use AppBundle\Entity\Tournament;
use AppBundle\Entity\TournamentStatistics;
use AppBundle\Form\TournamentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormError;
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
     * @Route("/{id}", name="_tournaments_page")
     *
     * @param Request $request
     * @param Tournament $tournament
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tournamentAction(Request $request, Tournament $tournament)
    {
        $statistics = $this->getDoctrine()
            ->getRepository('AppBundle:TournamentStatistics')
            ->findBy(array('tournament' => $tournament->getId()), array('points' => 'desc'));

        $games = $this->getDoctrine()
            ->getRepository('AppBundle:Game')
            ->findBy(array('tournament' => $tournament->getId(), 'stage' => Game::STAGE_GROUP));

        return $this->render(
            'AppBundle:Tournament:tournament.html.twig',
            array(
                'active' => 'tournaments',
                'statistics' => $statistics,
                'games' => $games,
                'tournament' => $tournament
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

                $this->redirectToRoute('_tournaments_list');
            }
        }

        return $this->render(
            'AppBundle:Tournament:create.html.twig',
            array('active' => 'tournaments', 'form' => $form->createView())
        );
    }

    /**
     * @param array $teams
     * @param Tournament $tournament
     * @return array
     */
    public function validateTeams($teams, Tournament $tournament)
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
    public function createGames($teams, Tournament $tournament)
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
        }

        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param Team[] $teams
     * @param Tournament $tournament
     */
    public function createStatistics($teams, Tournament $tournament)
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
}

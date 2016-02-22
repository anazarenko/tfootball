<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="_main_page")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $days = $request->request->get("days") ? $request->request->get("days") : 10;
        $gameRepository = $this->getDoctrine()->getRepository('AppBundle:Game');
        $lastGames = $gameRepository->findBy(array('status' => Game::STATUS_CONFIRMED), array('gameDate' => 'DESC'), 5);

        $gameQuery = $gameRepository
            ->getGamesByDate((new \DateTime('now'))->modify('-'.$days.' day'), new \DateTime('now'));

        /** @var Game[] $games */
        $games = $gameQuery->getResult();

        $sortingGames = array();
        $sortingTeams = array();

        foreach ($games as $game) {
            $firstTeamID = $game->getFirstTeam()->getId();
            $secondTeamID = $game->getSecondTeam()->getId();

            if (!array_key_exists($firstTeamID, $sortingGames)) {
                $sortingGames[$firstTeamID] = array(
                    'drawn' => array(),
                    'won' => array(),
                    'lost' => array(),
                    'team' => $game->getFirstTeam()
                );
            }

            if (!array_key_exists($secondTeamID, $sortingGames)) {
                $sortingGames[$secondTeamID] = array(
                    'drawn' => array(),
                    'won' => array(),
                    'lost' => array(),
                    'team' => $game->getSecondTeam()
                );
            }

            switch ($game->getResult()) {
                case 0:
                    $sortingGames[$firstTeamID]['drawn'][] = $game;
                    $sortingGames[$secondTeamID]['drawn'][] = $game;
                    break;
                case 1:
                    $sortingGames[$firstTeamID]['won'][] = $game;
                    $sortingGames[$secondTeamID]['lost'][] = $game;
                    break;
                case 2:
                    $sortingGames[$firstTeamID]['lost'][] = $game;
                    $sortingGames[$secondTeamID]['won'][] = $game;
                    break;
            }
        }

        foreach ($sortingGames as $key => &$item) {
            $countGame = count($item['won']) + count($item['drawn']) + count($item['lost']);
            $percentWon = (count($item['won'])/$countGame)*100;
            $item['gameCount'] = $countGame;
            $item['wonPercent'] = $percentWon;

            $sortingTeams[$key] = $percentWon;
        }

        arsort($sortingTeams);

        foreach ($sortingTeams as $key => $value) {
            $sortingTeams[$key] = $sortingGames[$key];
        }

        // If async request
        if ($request->isXmlHttpRequest()) {

            $single = $this->renderView(
                'AppBundle:Default:bestTable.html.twig',
                array('type' => 'single', 'bestTeams' => $sortingTeams)
            );

            $double = $this->renderView(
                'AppBundle:Default:bestTable.html.twig',
                array('type' => 'double', 'bestTeams' => $sortingTeams)
            );

            $data = array('status' => 1, 'single' => $single, 'double' => $double);

            $json = json_encode($data);
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return $this->render(
            'AppBundle:Default:index.html.twig',
            array(
                'active' => 'main',
                'lastGames' => $lastGames,
                'bestTeams' => $sortingTeams
            )
        );
    }
}

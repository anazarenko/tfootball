<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Confirm;
use AppBundle\Entity\Game;
use AppBundle\Entity\Team;
use AppBundle\Form\GameCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HeaderController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerGamePopupAction()
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

        return $this->render(
            'AppBundle:Header:headerGamePopup.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerGameNotifyAction()
    {
        /** @var \AppBundle\Entity\User $user */
        $user = $this->getUser();

        $confirmRepo = $this->getDoctrine()->getRepository('AppBundle:Confirm');
        $confirms = $confirmRepo->findBy(array('user' => $user, 'status' => Confirm::STATUS_NEW));

        return $this->render(
            'AppBundle:Header:notify.html.twig',
            array('confirms' => $confirms)
        );
    }

    /**
     * @Route("/team/stats", name="_team_stats")
     * @param Request $request
     * @return bool|Response
     */
    public function headerPopupStatistics(Request $request)
    {
        // Default date range
        $dateRange = $this->getParameter('app.start_date').'-'.date('d.m.Y', time());
        $dates = explode('-', $dateRange);

        // Get team repository
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');

        // Get game repository
        $gameRepository = $games = $this->getDoctrine()->getRepository('AppBundle:Game');

        $filterFirstTeam = $request->request->get('firstTeam') ? $request->request->get('firstTeam') : null;
        $filterSecondTeam = $request->request->get('secondTeam') ? $request->request->get('secondTeam') : null;

        /** @var Team $firstTeam */
        $firstTeam = $teamRepository->findTeamByMemberIDs($filterFirstTeam);
        /** @var Team $secondTeam */
        $secondTeam = $teamRepository->findTeamByMemberIDs($filterSecondTeam);

        if (!$this->get('app.team_service')->isValidTeams($filterFirstTeam, $filterSecondTeam, $errorMsg)) {
            if ($request->isXmlHttpRequest()) {

                $data = array('status' => 0, 'error' => $errorMsg);

                $json = json_encode($data);
                $response = new Response($json, 200);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            return false;
        }

        if (!$firstTeam || !$secondTeam) {
            if ($request->isXmlHttpRequest()) {

                $data = array('status' => 0, 'error' => 'Team doesn`t exist');

                $json = json_encode($data);
                $response = new Response($json, 200);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            return false;
        }

        // Get games query
        $gamesStatsQuery = $gameRepository
            ->getGamesByDate(
                new \DateTime($dates[0]),
                new \DateTime($dates[1]),
                $firstTeam,
                $secondTeam
            );

        // Get array of sorting matches for team
        $teamStats = $this->get('app.game_service')->parseGamesByPlayers($gamesStatsQuery->getResult());

        if (!count($teamStats)) {
            if ($request->isXmlHttpRequest()) {

                $data = array('status' => 0, 'error' => 'No matches');

                $json = json_encode($data);
                $response = new Response($json, 200);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            return false;
        }

        $firstTeamStats = $teamStats[$firstTeam->getId()];
        $secondTeamStats = $teamStats[$secondTeam->getId()];

        // Get games query
        $gamesQuery = $gameRepository
            ->getGamesByDate(
                new \DateTime($dates[0]),
                new \DateTime($dates[1]),
                $firstTeam,
                $secondTeam
            );

        $games = $gamesQuery->setMaxResults(5)->getResult();

        // If async request
        if ($request->isXmlHttpRequest()) {

            $html = $this->renderView(
                'AppBundle:Header:headerStats.html.twig',
                array(
                    'firstTeamStats' => $firstTeamStats,
                    'secondTeamStats' => $secondTeamStats,
//                    'games' => $this->renderView('AppBundle:Game:item.html.twig', array('games' => $games))
                    'games' => $games
                )
            );

            $data = array(
                'status' => 1,
                'html' => $html
            );

            $json = json_encode($data);
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return false;
    }
}

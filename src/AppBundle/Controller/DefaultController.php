<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        // Get game repository
        $gameRepository = $this->getDoctrine()->getRepository('AppBundle:Game');

        // Get last 5 games of all time
        $lastGames = $gameRepository->findBy(array('status' => Game::STATUS_CONFIRMED), array('gameDate' => 'DESC'), 5);

        // Get period of days
        $days = $request->request->get("days") ? $request->request->get("days") : 10;

        // Get array of sorting matches for team
        $sortingTeams = $this->get('app.game_service')->getBestPlayersArray($days);

        // If async request
        if ($request->isXmlHttpRequest()) {

            // Get single matches view
            $single = $this->renderView(
                'AppBundle:Default:bestTable.html.twig',
                array('type' => 'single', 'bestTeams' => $sortingTeams)
            );

            // Get double matches view
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

    /**
     * @Route("/bestplayers", name="_async_best_players")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function asyncBestPlayerAction(Request $request)
    {
        // Get period of days
        $days = $request->request->get("days") ? $request->request->get("days") : 10;

        // Get array of sorting matches for team
        $sortingTeams = $this->get('app.game_service')->getBestPlayersArray($days);

        // Get single matches view
        $single = $this->renderView(
            'AppBundle:Default:bestTable.html.twig',
            array('type' => 'single', 'bestTeams' => $sortingTeams)
        );

        // Get double matches view
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
}

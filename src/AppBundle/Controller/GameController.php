<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use AppBundle\Form\GameCreateType;
use DashboardBundle\Form\UserCreateType;
use DashboardBundle\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * @Route("/", name="_games")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function matchAction()
    {
        $games = $this->getDoctrine()
            ->getRepository('AppBundle:Game')
            ->findBy(array('status' => 1), array('gameDate' => 'DESC'));

        $game = new Game();
        $form = $this->createForm(
            new GameCreateType(array(
                'type' => $game->availableType,
                'status' => $game->availableStatus,
                'form' => $game->availableForm
            )),
            $game,
            array(
                'action' => $this->generateUrl('_game_create'),
                'method' => 'POST'
            )
        );

        return $this->render(
            'AppBundle:Game:index.html.twig',
            array('active' => 'games', 'games' => $games, 'form' => $form->createView())
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

            $firstPlayer = $game->getFirstPlayer();
            $secondPlayer = $game->getSecondPlayer();

            if ($firstPlayer == $secondPlayer) {
                if ($request->isXmlHttpRequest()) {
                    $data = array('status' => 0, 'error' => 'Oops. Players must be different.');
                    $json = json_encode($data);
                    $response = new Response($json, 200);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                } else {
                    return false;
                }
            }

            /** @var \AppBundle\Entity\User $user */
            $user = $this->getUser();

            $game->setCreator($user);

            $game->addPlayer($firstPlayer);
            $game->addPlayer($secondPlayer);

            $firstPlayer->addGame($game);
            $secondPlayer->addGame($game);

            if ($firstPlayer == $user) {
                $game->setConfirmedFirst(1);
            }
            if ($secondPlayer == $user) {
                $game->setConfirmedSecond(1);
            }

            if ($game->getFirstGoals() > $game->getSecondGoals()) {
                $game->setWinner($firstPlayer);
                $game->setLoser($secondPlayer);
            } elseif ($game->getFirstGoals() < $game->getSecondGoals()) {
                $game->setWinner($secondPlayer);
                $game->setLoser($firstPlayer);
            } elseif ($game->getFirstGoals() == $game->getSecondGoals()) {
                $game->addDrawn($firstPlayer);
                $game->addDrawn($secondPlayer);
                $firstPlayer->addDrawnGame($game);
                $secondPlayer->addDrawnGame($game);
            }

            $game->setStatus(0);

            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($game);
            $eManager->flush();

            if ($request->isXmlHttpRequest()) {

                $data = array('status' => 1);

                $json = json_encode($data);
                $response = new Response($json, 200);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

            // Add Flash message
            $this->addFlash('success', 'New Game was added!');
        } else {
            if ($request->isXmlHttpRequest()) {

                $data = array('status' => 0, 'error' => 'Oops. Something went wrong. Please try again.');

                $json = json_encode($data);
                $response = new Response($json, 200);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $this->addFlash('error', 'Error!');
        }

        return $this->redirectToRoute('_games');
    }
}

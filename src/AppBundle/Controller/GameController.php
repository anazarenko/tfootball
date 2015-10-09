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
            ->findAll();

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

            $firstPlayer = $game->getXFirstPlayer();
            $secondPlayer = $game->getYFirstPlayer();

            $game->addPlayer($firstPlayer);
            $game->addPlayer($secondPlayer);

            $firstPlayer->addGame($game);
            $secondPlayer->addGame($game);

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

            $game->setStatus(1);


            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($game);
            $eManager->flush();

            // Add Flash message
            $this->addFlash('success', 'New Game was added!');
        } else {
            $this->addFlash('error', 'Error!');
        }

        return $this->redirectToRoute('_games');
    }
}

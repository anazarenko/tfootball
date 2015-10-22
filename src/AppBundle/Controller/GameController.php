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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;

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
                $game->setResult($game::RESULT_FIRST_WINNER);
            } elseif ($game->getFirstGoals() < $game->getSecondGoals()) {
                $game->setWinner($secondPlayer);
                $game->setLoser($firstPlayer);
                $game->setResult($game::RESULT_SECOND_WINNER);
            } elseif ($game->getFirstGoals() == $game->getSecondGoals()) {
                $game->setResult($game::RESULT_DRAW);
            }

            $game->setStatus($game::STATUS_NEW);

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

            if ($game->getFirstPlayer() == $user || $game->getSecondPlayer() == $user) {

                if ($game->getFirstPlayer() == $user) {
                    $game->setConfirmedFirst($game::STATUS_COMPLETED);
                } elseif ($game->getSecondPlayer() == $user) {
                    $game->setConfirmedSecond($game::STATUS_COMPLETED);
                }

                if ($game->getConfirmedFirst() == $game::STATUS_COMPLETED && $game->getConfirmedSecond() == $game::STATUS_COMPLETED) {
                    $game->setStatus($game::STATUS_COMPLETED);
                }

                $eManager = $this->getDoctrine()->getManager();
//                $eManager->persist($game);
                $eManager->flush();

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

            if ($game->getFirstPlayer() == $user || $game->getSecondPlayer() == $user) {

                if ($game->getFirstPlayer() == $user) {
                    $game->setConfirmedFirst($game::STATUS_REJECTED);
                } elseif ($game->getSecondPlayer() == $user) {
                    $game->setConfirmedSecond($game::STATUS_REJECTED);
                }

                $game->setStatus($game::STATUS_REJECTED);

                $eManager = $this->getDoctrine()->getManager();
//                $eManager->persist($game);
                $eManager->flush();

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

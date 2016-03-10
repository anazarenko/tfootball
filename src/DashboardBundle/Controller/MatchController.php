<?php

namespace DashboardBundle\Controller;

use AppBundle\Entity\Confirm;
use AppBundle\Entity\Game;
use AppBundle\Entity\Statistics;
use AppBundle\Entity\User;
use DashboardBundle\Form\UserCreateType;
use DashboardBundle\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/matches")
 */
class MatchController extends Controller
{
    /**
     * @Route("/", name="_dashboard_matches")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function matchesAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $page = $request->query->get('page') ? $request->request->get('page') : 1;

        $gamesQuery = $entityManager->getRepository('AppBundle:Game')
            ->createQueryBuilder('g')->select('g')->orderBy('g.gameDate', 'DESC')->getQuery();

        // Create pagination
        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $gamesQuery, /* query NOT result */
            $page, /* page number */
            20 /* limit per page */
        );

        return $this->render(
            'DashboardBundle:Matches:index.html.twig',
            array('active' => 'matches', 'pagination' => $pagination)
        );
    }

    /**
     * @Route("/status/change/{id}", name="_dashboard_matches_status_change")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function statusAction(Request $request, Confirm $confirm)
    {
        // TODO: Optimize this controller
        $entityManager = $this->getDoctrine()->getManager();
        $game = $confirm->getGame();
        $oldGameStatus = $game->getStatus();
        $oldConfirmStatus = $confirm->getStatus();
        $gameStatus = null;
        $newConfirmStatus = $request->request->get('status');

        $confirm->setStatus($newConfirmStatus);

        $gameStatus = Game::STATUS_CONFIRMED;
        /** @var \AppBundle\Entity\Confirm $currentConfirm */
        foreach ($game->getConfirms() as $currentConfirm) {
//            if ($currentConfirm->getId() == $confirm->getId()) {
//                $currentConfirm->setStatus($newConfirmStatus);
//            }
            if ($currentConfirm->getStatus() == Confirm::STATUS_REJECTED) {
                $gameStatus = Game::STATUS_REJECTED;
                break;
            } elseif ($currentConfirm->getStatus() == Confirm::STATUS_NEW) {
                $gameStatus = Game::STATUS_NEW;
            }
        }

        if ($gameStatus != $oldGameStatus) {
            $game->setStatus($gameStatus);
            if ($gameStatus == Game::STATUS_CONFIRMED) {
                $this->get('app.team_service')->updateStatistics($game);
            } elseif ($oldGameStatus == Game::STATUS_CONFIRMED) {
                $this->get('app.team_service')->updateStatistics($game, Statistics::ACTION_REMOVE);
            }
        }

        $entityManager->flush();
        $responseStatus = 1;

        $json = json_encode(
            array(
                'status' => $responseStatus,
                'oldGameStatus' => strtolower($game->availableStatus[$oldGameStatus]),
                'newGameStatus' => strtolower($game->availableStatus[$gameStatus]),
                'oldConfirmStatus' => strtolower($confirm->availableStatus[$oldConfirmStatus]),
                'newConfirmStatus' => strtolower($confirm->availableStatus[$newConfirmStatus])
            )
        );
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }
}

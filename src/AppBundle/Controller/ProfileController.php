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
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/", name="_profile")
     * @Route("/{id}", name="_profile_id", requirements={"id" = "\d+"})
     * @param Request $request
     * @param int|null $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function profileAction(Request $request, $id = null)
    {
        // Default date range
        $dateRange = $this->getParameter('app.start_date').'-'.date('d.m.Y', time());
        $dates = explode('-', $dateRange);

        // Get team repository
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');

        // Get game repository
        $gameRepository = $games = $this->getDoctrine()->getRepository('AppBundle:Game');

        // Get page for pagination
        $page = (!empty($request->request->getInt('page'))) ? $request->request->getInt('page') : 1;

        if ($id) {
            $team = $teamRepository->findOneBy(array('id' => $id));
        } else {
            /** @var User $user */
            $user = $this->getUser();
            /** @var Team $team */
            $team = $teamRepository->createQueryBuilder('t')
                ->join('t.users', 'u')
                ->where('t.playerCount = 1')
                ->andWhere('u.id = :id')
                ->setParameter('id', $user->getId())
                ->getQuery()
                ->getOneOrNullResult();
        }

        if (!$team) {
            return $this->render(
                'AppBundle:Profile:index.html.twig',
                array(
                    'active' => 'profile',
                    'pagination' => '',
                    'name',
                    'moreBtn' => false
                )
            );
        }

        // Get games query
        $gamesQuery = $gameRepository
            ->getGamesByDate(
                new \DateTime($dates[0]),
                new \DateTime($dates[1]),
                $team
            );

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

        // Get games query
        $gamesStatsQuery = $gameRepository
            ->getGamesByDate(
                new \DateTime($dates[0]),
                new \DateTime($dates[1]),
                $team
            );

        // Get array of sorting matches for team
        $teamStats = $this->get('app.game_service')->parseGamesByPlayers($gamesStatsQuery->getResult());
        $teamStats = $teamStats[$team->getId()];

        return $this->render(
            'AppBundle:Profile:index.html.twig',
            array(
                'active' => 'profile',
                'pagination' => $pagination,
                'teamStats' => $teamStats,
                'moreBtn' => $moreBtn,
                'names' => $team->getPlayerNames()
            )
        );
    }
}

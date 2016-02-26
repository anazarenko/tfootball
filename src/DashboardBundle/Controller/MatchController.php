<?php

namespace DashboardBundle\Controller;

use AppBundle\Entity\User;
use DashboardBundle\Form\UserCreateType;
use DashboardBundle\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
}

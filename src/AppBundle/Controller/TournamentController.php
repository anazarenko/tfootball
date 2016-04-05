<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tournament;
use AppBundle\Form\TournamentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        return $this->render(
            'AppBundle:Tournament:index.html.twig',
            array('active' => 'tournaments')
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
            dump($tournament);
            die;
        }

        return $this->render(
            'AppBundle:Tournament:create.html.twig',
            array('active' => 'tournaments', 'form' => $form->createView())
        );
    }
}

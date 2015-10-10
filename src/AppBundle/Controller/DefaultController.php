<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Form\GameCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="_main_page")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Default:index.html.twig', array('active' => 'main'));
    }

    /**
     * @Route("/header/game", name="_header_game_popup")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerGamePopupAction()
    {
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
            'AppBundle::headerGamePopup.html.twig',
            array('form' => $form->createView())
        );
    }
}

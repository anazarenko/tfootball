<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Form\GameCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HeaderController extends Controller
{
    /**
     * @Route("/header/game/popup", name="_header_game_popup")
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
            'AppBundle:Header:headerGamePopup.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/header/game/notify", name="_header_game_notify")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerGameNotifyAction()
    {
        $user = $this->getUser();

        $games = $this->getDoctrine()
            ->getRepository('AppBundle:Game')
            ->getNotify($user);

        return $this->render(
            'AppBundle:Header:notify.html.twig',
            array('games' => $games)
        );
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Confirm;
use AppBundle\Entity\Game;
use AppBundle\Form\GameCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HeaderController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerGamePopupAction()
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

        return $this->render(
            'AppBundle:Header:headerGamePopup.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerGameNotifyAction()
    {
        /** @var \AppBundle\Entity\User $user */
        $user = $this->getUser();

        $confirmRepo = $this->getDoctrine()->getRepository('AppBundle:Confirm');
        $confirms = $confirmRepo->findBy(array('user' => $user, 'status' => Confirm::STATUS_NEW));

        return $this->render(
            'AppBundle:Header:notify.html.twig',
            array('confirms' => $confirms)
        );
    }
}

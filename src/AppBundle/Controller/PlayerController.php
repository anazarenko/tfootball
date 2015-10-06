<?php

namespace AppBundle\Controller;

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
 * @Route("/player")
 */
class PlayerController extends Controller
{
    /**
     * @Route("/", name="_player")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function playerAction()
    {
        $eManager = $this->getDoctrine()->getManager();
        $players = $eManager->getRepository('AppBundle:User')->findAll();
        return $this->render(
            'AppBundle:Player:index.html.twig',
            array('active' => 'players', 'players' => $players)
        );
    }
}

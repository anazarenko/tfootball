<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Form\GameRandomizerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/randomizer", name="_randomizer")
*/
class RandomizerController extends Controller
{
    /**
     * @Route("/", name="_randomizer")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new GameRandomizerType());

        return $this->render(
            'AppBundle:Randomizer:index.html.twig',
            array(
                'active' => 'randomizer',
                'randomForm' => $form->createView()
            )
        );
    }
}

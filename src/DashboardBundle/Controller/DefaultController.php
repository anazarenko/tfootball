<?php

namespace DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/dashboard")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render(
            'DashboardBundle:Main:main.html.twig',
            array('active' => 'dashboard')
        );
    }
}

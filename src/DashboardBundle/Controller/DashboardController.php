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
 * @Route("/admin")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="_dashboard")
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

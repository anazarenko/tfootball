<?php

namespace DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/dashboard/help")
 */
class HelpController extends Controller
{
    /**
     * @Route("/", name="dashboard_help")
     */
    public function indexAction()
    {
        return $this->render('@Dashboard/Help/main.html.twig');
    }

    /**
     * @Route("/blank", name="dashboard_blank_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function blankAction()
    {
        return $this->render('@Dashboard/Help/blank-page.html.twig');
    }

    /**
     * @Route("/elements", name="dashboard_elements")
     */
    public function elementsAction()
    {
        return $this->render('@Dashboard/Help/bootstrap-elements.html.twig');
    }

    /**
     * @Route("/grid", name="dashboard_grid")
     */
    public function gridAction()
    {
        return $this->render('@Dashboard/Help/bootstrap-grid.html.twig');
    }

    /**
     * @Route("/charts", name="dashboard_charts")
     */
    public function chartsAction()
    {
        return $this->render('@Dashboard/Help/charts.html.twig');
    }

    /**
     * @Route("/forms", name="dashboard_forms")
     */
    public function formsAction()
    {
        return $this->render('@Dashboard/Help/forms.html.twig');
    }

    /**
     * @Route("/rtl", name="dashboard_rtl")
     */
    public function rtlAction()
    {
        return $this->render('@Dashboard/Help/index-rtl.html.twig');
    }

    /**
     * @Route("/tables", name="dashboard_tables")
     */
    public function tablesAction()
    {
        return $this->render('@Dashboard/Help/tables.html.twig');
    }
}

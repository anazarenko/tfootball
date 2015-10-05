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

    /**
     * @Route("/users", name="_dashboard_users")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usersAction()
    {
        $eManager = $this->getDoctrine()->getManager();
        $users = $eManager->getRepository('AppBundle:User')->findAll();
        return $this->render(
            'DashboardBundle:Main:users.html.twig',
            array('active' => 'players', 'users' => $users)
        );
    }

    /**
     * @Route("/user/create", name="_dashboard_user_create")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(
            new UserCreateType(),
            $user,
            array(
                'action' => $this->generateUrl('_dashboard_user_create'),
                'method' => 'POST'
            )
        );

        if ($request->isXmlHttpRequest()) {

            $template = $this->renderView(
                '@Dashboard/Main/userCreateModal.html.twig',
                array('form' => $form->createView())
            );

            $json = json_encode($template);
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(array($user->availableRoles[$user->getRoles()]));
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($user);
            $eManager->flush();

            // Add Flash message
            $this->addFlash('success', 'New User was created!');
        } else {
            $this->addFlash('error', 'Error!');
        }

        return $this->redirectToRoute('_dashboard_users');
    }

    /**
     * @Route("/user/update/{id}", name="_dashboard_user_update", requirements={"id": "\d+"})
     * @Method("POST")
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateUserAction(User $user, Request $request)
    {
        $currentPassword = $user->getPassword();
        $form = $this->createForm(
            new UserEditType(),
            $user,
            array(
                'action' => $this->generateUrl('_dashboard_user_update', array('id' => $user->getId())),
                'method' => 'POST'
            )
        );

        if ($request->isXmlHttpRequest()) {

            $template = $this->renderView(
                '@Dashboard/Main/userEditModal.html.twig',
                array('form' => $form->createView())
            );

            $json = json_encode($template);
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(array($user->availableRoles[$user->getRoles()]));

            if ($user->getPassword() != '') {
                $currentPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
            }

            $user->setPassword($currentPassword);

            $eManager = $this->getDoctrine()->getManager();
            $eManager->flush();

            // Add Flash message
            $this->addFlash('success', 'User was updated!');
        } else {
            $this->addFlash('error', 'Error!');
        }

        return $this->redirectToRoute('_dashboard_users');
    }
}

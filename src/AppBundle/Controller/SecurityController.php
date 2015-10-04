<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login_route")
     */
    public function loginAction(Request $request)
    {
        $user = new User();
        $form = $this->createRegistrationForm($user, 'register');

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'AppBundle:Security:index.html.twig',
            array(
                'active' => 'login',
                'form' => $form->createView(),
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }

    /**
     * @Route("/login_check", name="login_check_route")
     */
    public function loginCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registrationAction(Request $request)
    {
        $user = new User();
        $form = $this->createRegistrationForm($user, 'register active');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $user->setRoles(array('ROLE_USER'));
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($user);
//            $eManager->flush();

            return $this->redirectToRoute('login_route');
        }

        return $this->render(
            'AppBundle:Security:index.html.twig',
            array(
                'active' => 'registration',
                'form' => $form->createView(),
                'last_username' => '',
                'error'         => '',
            )
        );
    }

    /**
     * @Route("/forgot", name="forgot_pass")
     */
    public function forgotAction()
    {
        return $this->render('AppBundle:Security:index.html.twig', array('active' => 'forgot'));
    }

    public function createRegistrationForm(User $user, $formClass)
    {
        $form = $this->createForm(
            new UserRegistrationType(),
            $user,
            array(
                'action' => $this->generateUrl('registration'),
                'method' => 'POST',
                'attr' => array('class' => $formClass)
            )
        );

        return $form;
    }
}

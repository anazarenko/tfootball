<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Form\SettingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{
    /**
     * @Route("/setting", name="_setting_page")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $oldPassword = $user->getPassword();
        $oldUsername = $user->getUsername();

        $user->setPassword('');

        $form = $this->createSettingForm($user, 'setting-form');

        $form->add('save', 'submit', array('attr' => array('class' => 'btn-primary')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->getPassword() != '') {
                $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
            } else {
                $user->setPassword($oldPassword);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            if ($user->getUsername() !== $oldUsername) {
                /** @var Team $team */
                foreach ($user->getTeams() as $team) {
                    $playerNames = array();
                    foreach($team->getPlayerNames() as $playerName) {
                        $playerNames[] = $playerName === $oldUsername ? $user->getUsername() : $playerName;
                    }
                    $team->setPlayerNames($playerNames);
                    $entityManager->flush();
                }
            }
        }

        return $this->render(
            'AppBundle:Setting:index.html.twig',
            array(
                'active' => 'setting',
                'form' => $form->createView()
            )
        );
    }

    public function createSettingForm(User $user, $formClass)
    {
        $form = $this->createForm(
            new SettingType(),
            $user,
            array(
                'action' => $this->generateUrl('_setting_page'),
                'method' => 'POST',
                'attr' => array('class' => $formClass)
            )
        );

        return $form;
    }
}

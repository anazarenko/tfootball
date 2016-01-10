<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Team;

class TeamExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('members', array($this, 'teamMembers')),
        );
    }

    /**
     * @param Team $team
     * @param string $separator
     * @return mixed
     */
    public function teamMembers(Team $team, $separator = '/')
    {
        $members = implode(' '.$separator.' ', $team->getPlayerNames());

        return $members;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'team_extension';
    }
}

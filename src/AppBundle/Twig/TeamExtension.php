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
        $members = '';

        /** @var \AppBundle\Entity\User $member */
        foreach ($team->getUsers() as $member) {
            $members .= $member->getUsername();
            $members.= " $separator ";
        }

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

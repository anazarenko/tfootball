<?php

namespace AppBundle\Service;

use AppBundle\Entity\Team;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class TeamManager
{
    protected $templating;
    protected $entityManager;

    public function __construct(EntityManager $entityManager, EngineInterface $templating)
    {
        $this->entityManager = $entityManager;
        $this->templating = $templating;
    }

    public function getTeamMembers(Team $team)
    {
        return $team->getUsers();
    }

}

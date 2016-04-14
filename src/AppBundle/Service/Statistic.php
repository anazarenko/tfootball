<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

class Statistic
{
    protected $templating;
    protected $entityManager;
    protected $container;

    public function __construct(EntityManager $entityManager, EngineInterface $templating, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->templating = $templating;
        $this->container = $container;
    }


}

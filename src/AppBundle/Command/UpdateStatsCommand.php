<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateStatsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stats:update')
            ->setDescription('Updating statistics');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $gameRepository = $entityManager->getRepository('AppBundle:Game');
        $teamRepository = $entityManager->getRepository('AppBundle:Team');
        $statRepository = $entityManager->getRepository('AppBundle:Statistics');

        $io = new SymfonyStyle($input, $output);
        $io->title('Updating statistics');

        foreach ($teamRepository->findAll() as $team) {
            $wonGames = count($gameRepository->getWonGames($team)->getResult());
            $drawnGames = count($gameRepository->getDrawnGames($team)->getResult());
            $lostGames = count($gameRepository->getLostGames($team)->getResult());

            $statistic = $statRepository->getStatistic($team);

            $statistic->setWon($wonGames);
            $statistic->setDrawn($drawnGames);
            $statistic->setLost($lostGames);
            $entityManager->flush();

            $io->note("Team {$team->getId()}: won {$wonGames}, drawn {$drawnGames}, lost {$lostGames}.");
        }
    }
}
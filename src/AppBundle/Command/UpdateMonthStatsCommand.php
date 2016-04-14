<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateMonthStatsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stats:update:month')
            ->setDescription('Updating statistics');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $gameRepository = $entityManager->getRepository('AppBundle:Game');
        $teamRepository = $entityManager->getRepository('AppBundle:Team');
        $statRepository = $entityManager->getRepository('AppBundle:Statistics');
        $teamService = $this->getContainer()->get('app.team_service');

        $io = new SymfonyStyle($input, $output);
        $io->title('Updating statistics');

        foreach ($teamRepository->findAll() as $team) {
            $currentDate = new \DateTime('2016-03');
            $endDate = new \DateTime('now');
            while ($currentDate <= $endDate) {
                $wonGames = count($gameRepository->getWonGames($team, $currentDate)->getResult());
                $drawnGames = count($gameRepository->getDrawnGames($team, $currentDate)->getResult());
                $lostGames = count($gameRepository->getLostGames($team, $currentDate)->getResult());
                $streak = $teamService->getStreak($team->getId());

                $statistic = $statRepository->getStatistic($team, (int)$currentDate->format('m'), (int)$currentDate->format('Y'));

                $statistic->setWon($wonGames);
                $statistic->setDrawn($drawnGames);
                $statistic->setLost($lostGames);
                $statistic->setStreak($streak);
                $entityManager->flush();

                $io->note("Team {$team->getId()}, {$currentDate->format('m-Y')}: won {$wonGames}, drawn {$drawnGames}, lost {$lostGames}.");

                $currentDate->modify('+ 1 month');
            }

            $wonGames = count($gameRepository->getWonGames($team)->getResult());
            $drawnGames = count($gameRepository->getDrawnGames($team)->getResult());
            $lostGames = count($gameRepository->getLostGames($team)->getResult());
            $streak = $teamService->getStreak($team->getId());

            $statistic = $statRepository->getStatistic($team);

            $statistic->setWon($wonGames);
            $statistic->setDrawn($drawnGames);
            $statistic->setLost($lostGames);
            $statistic->setStreak($streak);
            $entityManager->flush();

            $io->note("Team {$team->getId()}, All Time: won {$wonGames}, drawn {$drawnGames}, lost {$lostGames}.");
        }
    }
}
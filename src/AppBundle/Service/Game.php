<?php

namespace AppBundle\Service;

use AppBundle\Entity\Confirm;
use AppBundle\Entity\Statistics;
use AppBundle\Entity\Team as TeamEntity;
use AppBundle\Entity\User;
use AppBundle\Entity\Game as GameEntity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

class Game
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

    /**
     * @param int $days
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getBestPlayersArray($days = 10)
    {
        // Get game repository
        $gameRepository = $this->entityManager->getRepository('AppBundle:Game');

        // Get query for getting games
        $gameQuery = $gameRepository
            ->getGamesByDate((new \DateTime('now'))->modify('-'.$days.' day'), new \DateTime('now'));

        // Get array of sorting matches for team
        $sortingTeams = $this->parseGamesByPlayers($gameQuery->getResult(), true);

        return $sortingTeams;
    }

    /**
     * @param \AppBundle\Entity\Game[] $games
     * @param bool|false $sortingByPercent
     * @return array
     */
    public function parseGamesByPlayers($games, $sortingByPercent = false)
    {
        // Array for saving matches by team (key - team id, value = array with matches)
        $sortingGames = array();

        foreach ($games as $game) {
            $firstTeamID = $game->getFirstTeam()->getId();
            $secondTeamID = $game->getSecondTeam()->getId();

            if (!array_key_exists($firstTeamID, $sortingGames)) {
                $sortingGames[$firstTeamID] = array(
                    'drawn' => array(),
                    'won' => array(),
                    'lost' => array(),
                    'team' => $game->getFirstTeam(),
                    'greatVictories' => array(
                        'games' => array(),
                        'difference' => 0
                    ),
                    'greatDefeats' => array(
                        'games' => array(),
                        'difference' => 0
                    ),
                    'streak' => array()
                );
            }

            if (!array_key_exists($secondTeamID, $sortingGames)) {
                $sortingGames[$secondTeamID] = array(
                    'drawn' => array(),
                    'won' => array(),
                    'lost' => array(),
                    'team' => $game->getSecondTeam(),
                    'greatVictories' => array(
                        'games' => array(),
                        'difference' => 0
                    ),
                    'greatDefeats' => array(
                        'games' => array(),
                        'difference' => 0
                    )
                );
            }

            switch ($game->getResult()) {
                case GameEntity::RESULT_DRAW:
                    $sortingGames[$firstTeamID]['drawn'][] = $game;
                    $sortingGames[$firstTeamID]['streak'][] = 'drawn';
                    $sortingGames[$secondTeamID]['drawn'][] = $game;
                    $sortingGames[$secondTeamID]['streak'][] = 'drawn';
                    break;

                case GameEntity::RESULT_FIRST_WINNER:
                    $sortingGames[$firstTeamID]['won'][] = $game;
                    $sortingGames[$firstTeamID]['streak'][] = 'won';
                    $sortingGames[$secondTeamID]['lost'][] = $game;
                    $sortingGames[$secondTeamID]['streak'][] = 'lost';

                    if ($game->getDifference() > $sortingGames[$firstTeamID]['greatVictories']['difference']) {
                        $sortingGames[$firstTeamID]['greatVictories']['difference'] = $game->getDifference();
                        $sortingGames[$firstTeamID]['greatVictories']['games'] = array($game);
                    } elseif ($game->getDifference() == $sortingGames[$firstTeamID]['greatVictories']['difference']) {
                        $sortingGames[$firstTeamID]['greatVictories']['games'][] = $game;
                    }
                    if ($game->getDifference() > $sortingGames[$secondTeamID]['greatDefeats']['difference']) {
                        $sortingGames[$secondTeamID]['greatDefeats']['difference'] = $game->getDifference();
                        $sortingGames[$secondTeamID]['greatDefeats']['games'] = array($game);
                    } elseif ($game->getDifference() == $sortingGames[$secondTeamID]['greatDefeats']['difference']) {
                        $sortingGames[$secondTeamID]['greatDefeats']['games'][] = $game;
                    }
                    break;

                case GameEntity::RESULT_SECOND_WINNER:
                    $sortingGames[$firstTeamID]['lost'][] = $game;
                    $sortingGames[$firstTeamID]['streak'][] = 'lost';
                    $sortingGames[$secondTeamID]['won'][] = $game;
                    $sortingGames[$secondTeamID]['streak'][] = 'won';

                    if ($game->getDifference() > $sortingGames[$secondTeamID]['greatVictories']['difference']) {
                        $sortingGames[$secondTeamID]['greatVictories']['difference'] = $game->getDifference();
                        $sortingGames[$secondTeamID]['greatVictories']['games'] = array($game);
                    } elseif ($game->getDifference() == $sortingGames[$secondTeamID]['greatVictories']['difference']) {
                        $sortingGames[$secondTeamID]['greatVictories']['games'][] = $game;
                    }
                    if ($game->getDifference() > $sortingGames[$firstTeamID]['greatDefeats']['difference']) {
                        $sortingGames[$firstTeamID]['greatDefeats']['difference'] = $game->getDifference();
                        $sortingGames[$firstTeamID]['greatDefeats']['games'] = array($game);
                    } elseif ($game->getDifference() == $sortingGames[$firstTeamID]['greatDefeats']['difference']) {
                        $sortingGames[$firstTeamID]['greatDefeats']['games'][] = $game;
                    }
                    break;
            }
        }

        // Array for saving team percent of won games
        if ($sortingByPercent) {
            $sortingByValue = $this->sortByWonPercent($sortingGames);
        } else {
            $sortingByValue = $sortingGames;
        }

        // Save matches from games array to percent array
        foreach ($sortingByValue as $key => $value) {
            $sortingByValue[$key] = $sortingGames[$key];
        }

        return $sortingByValue;
    }

    /**
     * @param array $sortingGames
     * @return array
     */
    protected function sortByWonPercent(&$sortingGames)
    {
        $sortingByValue = array();

        foreach ($sortingGames as $key => &$item) {
            $countGame = count($item['won']) + count($item['drawn']) + count($item['lost']);
            if (!$countGame){
                $countGame = 1;
            }
            $percentWon = (count($item['won'])/$countGame)*100;
            $item['gameCount'] = $countGame;
            $item['wonPercent'] = $percentWon;

            $sortingByValue[$key] = $percentWon;
        }

        arsort($sortingByValue);

        return $sortingByValue;
    }

    /**
     * Method for removing game
     * @param GameEntity $game
     */
    protected function removeGame(GameEntity $game)
    {
        $this->entityManager->remove($game);
        $this->container->get('app.team_service')->updateStatistics($game, Statistics::ACTION_REMOVE);
    }

    /**
     * @param GameEntity $game
     * @param User $user
     * @param array $data game_create post variable
     * @return array
     */
    public function createGame(GameEntity $game, User $user, $data)
    {
        // Get user repository
        $userRepo = $this->entityManager->getRepository('AppBundle:User');
        // Variable for error text
        $errorMsg = '';

        // Game score validation
        if ($game->getFirstScore() === null || $game->getSecondScore() === null) {
            return array('status' => 0, 'error' => 'Incorrect data');
        }

        // Game team validation
        if (!$this->container->get('app.team_service')->isValidTeams($data['firstTeam'], $data['secondTeam'], $errorMsg)) {
            return array('status' => 0, 'error' => $errorMsg);
        }

        $firstTeamEntitiesArray = array();
        $secondTeamEntitiesArray = array();
        $userEntitiesArray = array();

        foreach($data['firstTeam'] as $userID) {
            // Get user in team
            $currentUser = $userRepo->findOneBy(array('id' => $userID));
            // Add user entity to team array
            $firstTeamEntitiesArray[] = $currentUser;
            // Add user entity to users array
            $userEntitiesArray[] = $currentUser;
            // Add current user to game
            $game->addPlayer($currentUser);
        }

        foreach($data['secondTeam'] as $userID) {
            // Get user in team
            $currentUser = $userRepo->findOneBy(array('id' => $userID));
            // Add user entity to team array
            $secondTeamEntitiesArray[] = $currentUser;
            // Add user entity to users array
            $userEntitiesArray[] = $currentUser;
            // Add current user to game
            $game->addPlayer($currentUser);
        }

        $firstTeam = $this->container->get('app.team_service')->getTeam($firstTeamEntitiesArray);
        $secondTeam = $this->container->get('app.team_service')->getTeam($secondTeamEntitiesArray);

        $game->setFirstTeam($firstTeam);
        $game->setSecondTeam($secondTeam);
        $game->setType(GameEntity::TYPE_FRIENDLY);
        $game->setCreator($user);
        $game->setDifference(abs($game->getFirstScore() - $game->getSecondScore()));

        if ($game->getFirstScore() > $game->getSecondScore()) {
            $game->setResult(GameEntity::RESULT_FIRST_WINNER);
            $game->setWinner($firstTeam);
            $game->setLoser($secondTeam);
        } elseif ($game->getFirstScore() < $game->getSecondScore()) {
            $game->setResult(GameEntity::RESULT_SECOND_WINNER);
            $game->setWinner($secondTeam);
            $game->setLoser($firstTeam);
        } elseif ($game->getFirstScore() == $game->getSecondScore()) {
            $game->setResult(GameEntity::RESULT_DRAW);
        }

        $game->setStatus($game::STATUS_NEW);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        /** @var \AppBundle\Entity\User $currentUser */
        foreach ($userEntitiesArray as $currentUser) {
            $currentUser->addGame($game);
            // Create new confirm entity
            $confirm = new Confirm();
            $confirm->setGame($game);
            $confirm->setUser($currentUser);
            $confirm->setStatus($currentUser == $user ? Confirm::STATUS_CONFIRMED : Confirm::STATUS_NEW);

            $this->entityManager->persist($confirm);
            $this->entityManager->persist($currentUser);
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return array('status' => 1, 'error' => 'New Game was added!');
    }

    /**
     * @param GameEntity $game
     * @param User $user
     * @return array
     */
    public function acceptGame(GameEntity $game, User $user)
    {
        $confirmRepo = $this->entityManager->getRepository('AppBundle:Confirm');
        $confirm = $confirmRepo->findOneBy(array('user' => $user, 'game' => $game));

        if ($confirm) {
            $confirm->setStatus(Confirm::STATUS_CONFIRMED);

            if ($game->getStatus() != GameEntity::STATUS_REJECTED) {

                if ($this->isCompleteGame($game)) {
                    $game->setStatus(GameEntity::STATUS_CONFIRMED);
                    $this->container->get('app.team_service')->updateStatistics($game);
                }
            }

            $this->entityManager->flush();

            return array('status' => 1);
        } else {
            return array('status' => 0);
        }
    }

    /**
     * @param GameEntity $game
     * @param User $user
     * @return array
     */
    public function declineGame(GameEntity $game, User $user)
    {
        $confirmRepo = $this->entityManager->getRepository('AppBundle:Confirm');
        $confirm = $confirmRepo->findOneBy(array('user' => $user, 'game' => $game));

        if ($confirm) {
            $confirm->setStatus(Confirm::STATUS_REJECTED);
            $game->setStatus(GameEntity::STATUS_REJECTED);
            $this->entityManager->flush();
            return array('status' => 1);
        } else {
            return array('status' => 0);
        }
    }

    /**
     * Get team game result (1 - win, 0 - draw, -1 - lose)
     * @param GameEntity $game
     * @param TeamEntity $team
     * @return int
     */
    public function getTeamGameResult(GameEntity $game, TeamEntity $team)
    {
        switch ($game->getResult()) {
            case 1 :
                if ($game->getFirstTeam() === $team) {
                    return 1;
                } else {
                    return -1;
                }
                break;
            case 2 :
                if ($game->getSecondTeam() === $team) {
                    return 1;
                } else {
                    return -1;
                }
                break;
            default :
                return 0;
        }
    }

    /**
     * @param GameEntity $game
     * @return bool
     */
    public function isCompleteGame(GameEntity $game)
    {
        $isCompleteGame = true;
        /** @var \AppBundle\Entity\Confirm $currentConfirm */
        foreach ($game->getConfirms() as $currentConfirm) {
            if ($currentConfirm->getStatus() != Confirm::STATUS_CONFIRMED) {
                $isCompleteGame = false;
                break;
            }
        }

        return $isCompleteGame;
    }

}

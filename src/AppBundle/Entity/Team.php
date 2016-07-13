<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="teams")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TeamRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Team
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $playerCount;

    /**
     * @ORM\Column(type="array")
     */
    private $playerNames;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="teams")
     * @ORM\JoinTable(name="users_teams")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Statistics", mappedBy="team")
     */
    private $statistics;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Game", mappedBy="winner")
     */
    private $wonGames;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Game", mappedBy="loser")
     */
    private $lostGames;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tournament", mappedBy="teams", cascade={"persist"})
     */
    private $tournaments;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setModifiedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->wonGames = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lostGames = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set playerCount
     *
     * @param integer $playerCount
     * @return Team
     */
    public function setPlayerCount($playerCount)
    {
        $this->playerCount = $playerCount;

        return $this;
    }

    /**
     * Get playerCount
     *
     * @return integer 
     */
    public function getPlayerCount()
    {
        return $this->playerCount;
    }

    /**
     * Set playerNames
     *
     * @param array $playerNames
     * @return Team
     */
    public function setPlayerNames($playerNames)
    {
        $this->playerNames = $playerNames;

        return $this;
    }

    /**
     * Get playerNames
     *
     * @return array 
     */
    public function getPlayerNames()
    {
        return $this->playerNames;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Team
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     * @return Team
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime 
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Add users
     *
     * @param \AppBundle\Entity\User $users
     * @return Team
     */
    public function addUser(\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add statistics
     *
     * @param \AppBundle\Entity\Statistics $statistics
     * @return Team
     */
    public function addStatistic(\AppBundle\Entity\Statistics $statistics)
    {
        $this->statistics[] = $statistics;

        return $this;
    }

    /**
     * Remove statistics
     *
     * @param \AppBundle\Entity\Statistics $statistics
     */
    public function removeStatistic(\AppBundle\Entity\Statistics $statistics)
    {
        $this->statistics->removeElement($statistics);
    }

    /**
     * Get statistics
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStatistics()
    {
        return $this->statistics;
    }

    /**
     * Add wonGames
     *
     * @param \AppBundle\Entity\Game $wonGames
     * @return Team
     */
    public function addWonGame(\AppBundle\Entity\Game $wonGames)
    {
        $this->wonGames[] = $wonGames;

        return $this;
    }

    /**
     * Remove wonGames
     *
     * @param \AppBundle\Entity\Game $wonGames
     */
    public function removeWonGame(\AppBundle\Entity\Game $wonGames)
    {
        $this->wonGames->removeElement($wonGames);
    }

    /**
     * Get wonGames
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWonGames()
    {
        return $this->wonGames;
    }

    /**
     * Add lostGames
     *
     * @param \AppBundle\Entity\Game $lostGames
     * @return Team
     */
    public function addLostGame(\AppBundle\Entity\Game $lostGames)
    {
        $this->lostGames[] = $lostGames;

        return $this;
    }

    /**
     * Remove lostGames
     *
     * @param \AppBundle\Entity\Game $lostGames
     */
    public function removeLostGame(\AppBundle\Entity\Game $lostGames)
    {
        $this->lostGames->removeElement($lostGames);
    }

    /**
     * Get lostGames
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLostGames()
    {
        return $this->lostGames;
    }

    /**
     * Add tournaments
     *
     * @param \AppBundle\Entity\Tournament $tournaments
     * @return Team
     */
    public function addTournament(\AppBundle\Entity\Tournament $tournaments)
    {
        $this->tournaments[] = $tournaments;

        return $this;
    }

    /**
     * Remove tournaments
     *
     * @param \AppBundle\Entity\Tournament $tournaments
     */
    public function removeTournament(\AppBundle\Entity\Tournament $tournaments)
    {
        $this->tournaments->removeElement($tournaments);
    }

    /**
     * Get tournaments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTournaments()
    {
        return $this->tournaments;
    }
}

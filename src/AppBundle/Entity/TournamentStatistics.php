<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Statistics
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TournamentStatisticsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TournamentStatistics
{

    const ACTION_REMOVE = 0;
    const ACTION_ADD = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tournament", inversedBy="statistics")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
     */
    private $tournament;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="statistics")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"default" = 0})
     */
    private $won = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"default" = 0})
     */
    private $drawn = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"default" = 0})
     */
    private $lost = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"default" = 0})
     */
    private $gameCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="float", options={"default" = 0})
     */
    private $wonPercentage = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"default" = 0})
     */
    private $points = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;

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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedGamesCount()
    {
        $this->setGameCount($this->getWon() + $this->getDrawn() + $this->getLost());
        if ($this->getGameCount() > 0) {
            $this->setWonPercentage(round((($this->getWon() / $this->getGameCount()) * 100), 1));
            $this->setPoints($this->getWon() * 3 + $this->getDrawn() * 1);
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
     * Get modifiedAt
     *
     * @return \DateTime 
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    public function addWon()
    {
        $this->setWon($this->getWon() + 1);
    }

    public function addDrawn()
    {
        $this->setDrawn($this->getDrawn() + 1);
    }

    public function addLost()
    {
        $this->setLost($this->getLost() + 1);
    }

    public function removeWon()
    {
        $this->setWon($this->getWon() - 1);
    }

    public function removeDrawn()
    {
        $this->setDrawn($this->getDrawn() - 1);
    }

    public function removeLost()
    {
        $this->setLost($this->getLost() - 1);
    }

    /**
     * Set won
     *
     * @param integer $won
     * @return TournamentStatistics
     */
    public function setWon($won)
    {
        $this->won = $won;

        return $this;
    }

    /**
     * Get won
     *
     * @return integer 
     */
    public function getWon()
    {
        return $this->won;
    }

    /**
     * Set drawn
     *
     * @param integer $drawn
     * @return TournamentStatistics
     */
    public function setDrawn($drawn)
    {
        $this->drawn = $drawn;

        return $this;
    }

    /**
     * Get drawn
     *
     * @return integer 
     */
    public function getDrawn()
    {
        return $this->drawn;
    }

    /**
     * Set lost
     *
     * @param integer $lost
     * @return TournamentStatistics
     */
    public function setLost($lost)
    {
        $this->lost = $lost;

        return $this;
    }

    /**
     * Get lost
     *
     * @return integer 
     */
    public function getLost()
    {
        return $this->lost;
    }

    /**
     * Set gameCount
     *
     * @param integer $gameCount
     * @return TournamentStatistics
     */
    public function setGameCount($gameCount)
    {
        $this->gameCount = $gameCount;

        return $this;
    }

    /**
     * Get gameCount
     *
     * @return integer 
     */
    public function getGameCount()
    {
        return $this->gameCount;
    }

    /**
     * Set wonPercentage
     *
     * @param float $wonPercentage
     * @return TournamentStatistics
     */
    public function setWonPercentage($wonPercentage)
    {
        $this->wonPercentage = $wonPercentage;

        return $this;
    }

    /**
     * Get wonPercentage
     *
     * @return float 
     */
    public function getWonPercentage()
    {
        return $this->wonPercentage;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return TournamentStatistics
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
     * @return TournamentStatistics
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Set tournament
     *
     * @param \AppBundle\Entity\Tournament $tournament
     * @return TournamentStatistics
     */
    public function setTournament(\AppBundle\Entity\Tournament $tournament = null)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return \AppBundle\Entity\Tournament 
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * Set team
     *
     * @param \AppBundle\Entity\Team $team
     * @return TournamentStatistics
     */
    public function setTeam(\AppBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \AppBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return TournamentStatistics
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return TournamentStatistics
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
}

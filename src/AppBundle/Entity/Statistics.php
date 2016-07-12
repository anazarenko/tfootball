<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Statistics
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\StatisticsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Statistics
{
    const STREAK_COUNT = 5;

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
     * @ORM\Column(type="smallint", options={"default" = 0})
     */
    private $month;

    /**
     * @ORM\Column(type="smallint", options={"default" = 0})
     */
    private $year;

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
     * @var array
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $streak;

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
     * Set month
     *
     * @param integer $month
     * @return Statistics
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Statistics
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set won
     *
     * @param integer $won
     * @return Statistics
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
     * @return Statistics
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
     * @return Statistics
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
     * @return Statistics
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
     * @param integer $wonPercentage
     * @return Statistics
     */
    public function setWonPercentage($wonPercentage)
    {
        $this->wonPercentage = $wonPercentage;

        return $this;
    }

    /**
     * Get wonPercentage
     *
     * @return integer 
     */
    public function getWonPercentage()
    {
        return $this->wonPercentage;
    }


    /**
     * Set streak
     *
     * @param array $streak
     * @return Statistics
     */
    public function setStreak($streak)
    {
        $this->streak = $streak;

        return $this;
    }

    /**
     * Get streak
     *
     * @return array
     */
    public function getStreak()
    {
        return $this->streak;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Statistics
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
     * @return Statistics
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

    public function addWon()
    {
        $this->setWon($this->getWon() + 1);

        // Update streak
        $streak = $this->getStreak() ?: array();
        if (count($streak) === self::STREAK_COUNT) {
            array_pop($streak);
        }
        array_unshift($streak, 'won');
        $this->setStreak($streak);
    }

    public function addDrawn()
    {
        $this->setDrawn($this->getDrawn() + 1);

        // Update streak
        $streak = $this->getStreak() ?: array();
        if (count($streak) === self::STREAK_COUNT) {
            array_pop($streak);
        }
        array_unshift($streak, 'drawn');
        $this->setStreak($streak);
    }

    public function addLost()
    {
        $this->setLost($this->getLost() + 1);

        // Update streak
        $streak = $this->getStreak() ?: array();
        if (count($streak) === self::STREAK_COUNT) {
            array_pop($streak);
        }
        array_unshift($streak, 'lost');
        $this->setStreak($streak);
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
     * Set team
     *
     * @param \AppBundle\Entity\Team $team
     * @return Statistics
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
}

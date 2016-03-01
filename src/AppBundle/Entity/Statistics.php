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
     * @ORM\OneToOne(targetEntity="Team", inversedBy="statistics")
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
     * @ORM\Column(type="integer", options={"default" = 0})
     */
    private $wonPercentage = 0;

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
            $this->setWonPercentage(round(($this->getWon() / $this->getGameCount()) * 100));
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
}

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
     * @var integer
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $biggestVictories;

    /**
     * @var integer
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $biggestDefeats;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", options={"default" = 0})
     */
    private $biggestVictoryDifference = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", options={"default" = 0})
     */
    private $biggestDefeatsDifference = 0;

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
     * Set biggestVictories
     *
     * @param array $biggestVictories
     * @return Statistics
     */
    public function setBiggestVictories($biggestVictories)
    {
        $this->biggestVictories = $biggestVictories;

        return $this;
    }

    /**
     * Get biggestVictories
     *
     * @return array 
     */
    public function getBiggestVictories()
    {
        return $this->biggestVictories;
    }

    /**
     * Set biggestDefeats
     *
     * @param array $biggestDefeats
     * @return Statistics
     */
    public function setBiggestDefeats($biggestDefeats)
    {
        $this->biggestDefeats = $biggestDefeats;

        return $this;
    }

    /**
     * Get biggestDefeats
     *
     * @return array 
     */
    public function getBiggestDefeats()
    {
        return $this->biggestDefeats;
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

    public function updateBiggestVictory($difference, $gameId) {
        if ($this->getBiggestVictoryDifference() == $difference) {
            $biggestVictories = $this->getBiggestVictories();
            $biggestVictories[] = $gameId;
            $this->setBiggestVictories($biggestVictories);
        } elseif ($this->getBiggestVictoryDifference() < $difference) {
            $this->setBiggestVictories(array($gameId));
            $this->setBiggestVictoryDifference($difference);
        }
    }

    public function updateBiggestDefeats($difference, $gameId) {
        if ($this->getBiggestDefeatsDifference() == $difference) {
            $biggestDefeats = $this->getBiggestDefeats();
            $biggestDefeats[] = $gameId;
            $this->setBiggestDefeats($biggestDefeats);
        } elseif ($this->getBiggestDefeatsDifference() < $difference) {
            $this->setBiggestDefeats(array($gameId));
            $this->setBiggestDefeatsDifference($difference);
        }
    }

    /**
     * Set biggestVictoryDifference
     *
     * @param array $biggestVictoryDifference
     * @return Statistics
     */
    public function setBiggestVictoryDifference($biggestVictoryDifference)
    {
        $this->biggestVictoryDifference = $biggestVictoryDifference;

        return $this;
    }

    /**
     * Get biggestVictoryDifference
     *
     * @return array 
     */
    public function getBiggestVictoryDifference()
    {
        return $this->biggestVictoryDifference;
    }

    /**
     * Set biggestDefeatsDifference
     *
     * @param array $biggestDefeatsDifference
     * @return Statistics
     */
    public function setBiggestDefeatsDifference($biggestDefeatsDifference)
    {
        $this->biggestDefeatsDifference = $biggestDefeatsDifference;

        return $this;
    }

    /**
     * Get biggestDefeatsDifference
     *
     * @return array 
     */
    public function getBiggestDefeatsDifference()
    {
        return $this->biggestDefeatsDifference;
    }
}

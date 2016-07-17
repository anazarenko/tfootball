<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tournament
 *
 * @ORM\Table(name="tournaments")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TournamentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Tournament
{
    const FORM_SINGLE = 1;
    const FORM_DOUBLE = 2;

    const STATUS_GROUP_STAGE = 0;
    const STATUS_PLAYOFF = 1;
    const STATUS_FINISHED = 2;
    const STATUS_REJECTED = 3;

    const STAGE_GROUP = 0;
    const STAGE_FINAL = 1;
    const STAGE_SEMIFINAL = 2;
    const STAGE_QUARTERFINAL = 4;
    const STAGE_8thFINAL = 8;
    const STAGE_16thFINAL = 16;
    const STAGE_32thFINAL = 32;
    const STAGE_64thFINAL = 64;

    public static $availableStages = array(
        self::STAGE_GROUP => 'Group',
        self::STAGE_FINAL => 'Final',
        self::STAGE_SEMIFINAL => 'Semi-final',
        self::STAGE_QUARTERFINAL => 'Quarter-final',
        self::STAGE_8thFINAL => '1/8',
        self::STAGE_16thFINAL => '1/16',
        self::STAGE_32thFINAL => '1/32',
        self::STAGE_64thFINAL => '1/64',
    );

    public $availableForm = array(
        self::FORM_SINGLE => 'Single',
        self::FORM_DOUBLE => 'Double'
    );

    public $availableStatus = array(
        self::STATUS_GROUP_STAGE => 'In Progress',
        self::STATUS_PLAYOFF => 'In Progress',
        self::STATUS_FINISHED => 'Finished',
        self::STATUS_REJECTED => 'Rejected'
    );

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
    private $form;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $status = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $regularGameCount;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $playoffGameCount;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $finalGameCount;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $playoffTeamCount;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $currentStage = \AppBundle\Entity\Game::STAGE_GROUP;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_creator_id", referencedColumnName="id")
     */
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Game", mappedBy="tournament")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TournamentStatistics", mappedBy="tournament")
     */
    private $statistics;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Team", inversedBy="tournaments", cascade={"persist"})
     * @ORM\JoinTable(name="tournament_teams")
     */
    private $teams;

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
        $this->games = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set form
     *
     * @param integer $form
     * @return Tournament
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return integer
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set regularGameCount
     *
     * @param integer $regularGameCount
     * @return Tournament
     */
    public function setRegularGameCount($regularGameCount)
    {
        $this->regularGameCount = $regularGameCount;

        return $this;
    }

    /**
     * Get regularGameCount
     *
     * @return integer 
     */
    public function getRegularGameCount()
    {
        return $this->regularGameCount;
    }

    /**
     * Set playoffGameCount
     *
     * @param integer $playoffGameCount
     * @return Tournament
     */
    public function setPlayoffGameCount($playoffGameCount)
    {
        $this->playoffGameCount = $playoffGameCount;

        return $this;
    }

    /**
     * Get playoffGameCount
     *
     * @return integer 
     */
    public function getPlayoffGameCount()
    {
        return $this->playoffGameCount;
    }

    /**
     * Set finalGameCount
     *
     * @param integer $finalGameCount
     * @return Tournament
     */
    public function setFinalGameCount($finalGameCount)
    {
        $this->finalGameCount = $finalGameCount;

        return $this;
    }

    /**
     * Get finalGameCount
     *
     * @return integer 
     */
    public function getFinalGameCount()
    {
        return $this->finalGameCount;
    }

    /**
     * Set playoffTeamCount
     *
     * @param integer $playoffTeamCount
     * @return Tournament
     */
    public function setPlayoffTeamCount($playoffTeamCount)
    {
        $this->playoffTeamCount = $playoffTeamCount;

        return $this;
    }

    /**
     * Get playoffTeamCount
     *
     * @return integer 
     */
    public function getPlayoffTeamCount()
    {
        return $this->playoffTeamCount;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Tournament
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
     * @return Tournament
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
     * Set creator
     *
     * @param \AppBundle\Entity\User $creator
     * @return Tournament
     */
    public function setCreator(\AppBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \AppBundle\Entity\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Add games
     *
     * @param \AppBundle\Entity\Game $games
     * @return Tournament
     */
    public function addGame(\AppBundle\Entity\Game $games)
    {
        $this->games[] = $games;

        return $this;
    }

    /**
     * Remove games
     *
     * @param \AppBundle\Entity\Game $games
     */
    public function removeGame(\AppBundle\Entity\Game $games)
    {
        $this->games->removeElement($games);
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Add statistics
     *
     * @param \AppBundle\Entity\TournamentStatistics $statistics
     * @return Tournament
     */
    public function addStatistic(\AppBundle\Entity\TournamentStatistics $statistics)
    {
        $this->statistics[] = $statistics;

        return $this;
    }

    /**
     * Remove statistics
     *
     * @param \AppBundle\Entity\TournamentStatistics $statistics
     */
    public function removeStatistic(\AppBundle\Entity\TournamentStatistics $statistics)
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
     * Add teams
     *
     * @param \AppBundle\Entity\Team $teams
     * @return Tournament
     */
    public function addTeam(\AppBundle\Entity\Team $teams)
    {
        $this->teams[] = $teams;

        return $this;
    }

    /**
     * Remove teams
     *
     * @param \AppBundle\Entity\Team $teams
     */
    public function removeTeam(\AppBundle\Entity\Team $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Tournament
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set currentStage
     *
     * @param integer $currentStage
     * @return Tournament
     */
    public function setCurrentStage($currentStage)
    {
        $this->currentStage = $currentStage;

        return $this;
    }

    /**
     * Get currentStage
     *
     * @return integer 
     */
    public function getCurrentStage()
    {
        return $this->currentStage;
    }
}

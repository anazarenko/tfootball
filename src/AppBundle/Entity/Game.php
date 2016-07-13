<?php

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="games")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\GameRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Game
{
    const TYPE_FRIENDLY = 0;
    const TYPE_TOURNAMENT = 1;

    const STATUS_NEW = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_TOURNAMENT_WAITING = 3;

    const FORM_SINGLE = 1;
    const FORM_DOUBLE = 2;

    const RESULT_FIRST_WINNER = 1;
    const RESULT_SECOND_WINNER = 2;
    const RESULT_DRAW = 0;

    const STAGE_GROUP = 0;
    const STAGE_FINAL = 1;
    const STAGE_SEMIFINAL = 2;
    const STAGE_QUARTERFINAL = 4;
    const STAGE_8thFINAL = 8;
    const STAGE_16thFINAL = 16;
    const STAGE_32thFINAL = 32;
    const STAGE_64thFINAL = 64;

    public $availableStatus = array(
        self::STATUS_NEW => 'New',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_TOURNAMENT_WAITING => 'Tournament waiting'
    );

    public $availableStage = array(
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
        self::FORM_SINGLE => '1x1',
        self::FORM_DOUBLE => '2x2'
    );

    public $availableType = array(
        self::TYPE_FRIENDLY => 'Friendly',
        self::TYPE_TOURNAMENT => 'Tournament'
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
     * @ORM\Column(type="smallint")
     */
    private $status = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $form = 1;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $stage;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tournament", inversedBy="games")
     * @ORM\JoinColumn(name="tournament", referencedColumnName="id")
     */
    private $tournament;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team")
     * @ORM\JoinColumn(name="first_team", referencedColumnName="id")
     */
    private $firstTeam;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team")
     * @ORM\JoinColumn(name="second_team", referencedColumnName="id")
     */
    private $secondTeam;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(value = 0)
     * @Assert\LessThanOrEqual(value=2)
     */
    private $result;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(value = 0)
     */
    private $firstScore;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(value = 0)
     */
    private $secondScore;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $difference;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team", inversedBy="wonGames")
     * @ORM\JoinColumn(name="winner", referencedColumnName="id")
     */
    private $winner;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team", inversedBy="lostGames")
     * @ORM\JoinColumn(name="loser", referencedColumnName="id")
     */
    private $loser;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $gameDate;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_creator_id", referencedColumnName="id")
     */
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Confirm", mappedBy="game", cascade={"remove"})
     */
    private $confirms;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="games")
     **/
    private $players;

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

        if ($this->getGameDate() == null) {
            $this->setGameDate(new \DateTime('now'));
        }
    }

    public function __construct() {
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set status
     *
     * @param integer $status
     * @return Game
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
     * Set form
     *
     * @param integer $form
     * @return Game
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
     * Set type
     *
     * @param integer $type
     * @return Game
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set stage
     *
     * @param integer $stage
     * @return Game
     */
    public function setStage($stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return integer 
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Set result
     *
     * @param integer $result
     * @return Game
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return integer 
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set firstScore
     *
     * @param integer $firstScore
     * @return Game
     */
    public function setFirstScore($firstScore)
    {
        $this->firstScore = $firstScore;

        return $this;
    }

    /**
     * Get firstScore
     *
     * @return integer 
     */
    public function getFirstScore()
    {
        return $this->firstScore;
    }

    /**
     * Set secondScore
     *
     * @param integer $secondScore
     * @return Game
     */
    public function setSecondScore($secondScore)
    {
        $this->secondScore = $secondScore;

        return $this;
    }

    /**
     * Get secondScore
     *
     * @return integer 
     */
    public function getSecondScore()
    {
        return $this->secondScore;
    }

    /**
     * Set difference
     *
     * @param integer $difference
     * @return Game
     */
    public function setDifference($difference)
    {
        $this->difference = $difference;

        return $this;
    }

    /**
     * Get difference
     *
     * @return integer 
     */
    public function getDifference()
    {
        return $this->difference;
    }

    /**
     * Set gameDate
     *
     * @param \DateTime $gameDate
     * @return Game
     */
    public function setGameDate($gameDate)
    {
        $this->gameDate = $gameDate;

        return $this;
    }

    /**
     * Get gameDate
     *
     * @return \DateTime 
     */
    public function getGameDate()
    {
        return $this->gameDate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Game
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
     * @return Game
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
     * Set tournament
     *
     * @param \AppBundle\Entity\Tournament $tournament
     * @return Game
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
     * Set firstTeam
     *
     * @param \AppBundle\Entity\Team $firstTeam
     * @return Game
     */
    public function setFirstTeam(\AppBundle\Entity\Team $firstTeam = null)
    {
        $this->firstTeam = $firstTeam;

        return $this;
    }

    /**
     * Get firstTeam
     *
     * @return \AppBundle\Entity\Team 
     */
    public function getFirstTeam()
    {
        return $this->firstTeam;
    }

    /**
     * Set secondTeam
     *
     * @param \AppBundle\Entity\Team $secondTeam
     * @return Game
     */
    public function setSecondTeam(\AppBundle\Entity\Team $secondTeam = null)
    {
        $this->secondTeam = $secondTeam;

        return $this;
    }

    /**
     * Get secondTeam
     *
     * @return \AppBundle\Entity\Team 
     */
    public function getSecondTeam()
    {
        return $this->secondTeam;
    }

    /**
     * Set winner
     *
     * @param \AppBundle\Entity\Team $winner
     * @return Game
     */
    public function setWinner(\AppBundle\Entity\Team $winner = null)
    {
        $this->winner = $winner;

        return $this;
    }

    /**
     * Get winner
     *
     * @return \AppBundle\Entity\Team 
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * Set loser
     *
     * @param \AppBundle\Entity\Team $loser
     * @return Game
     */
    public function setLoser(\AppBundle\Entity\Team $loser = null)
    {
        $this->loser = $loser;

        return $this;
    }

    /**
     * Get loser
     *
     * @return \AppBundle\Entity\Team 
     */
    public function getLoser()
    {
        return $this->loser;
    }

    /**
     * Set creator
     *
     * @param \AppBundle\Entity\User $creator
     * @return Game
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
     * Add confirms
     *
     * @param \AppBundle\Entity\Confirm $confirms
     * @return Game
     */
    public function addConfirm(\AppBundle\Entity\Confirm $confirms)
    {
        $this->confirms[] = $confirms;

        return $this;
    }

    /**
     * Remove confirms
     *
     * @param \AppBundle\Entity\Confirm $confirms
     */
    public function removeConfirm(\AppBundle\Entity\Confirm $confirms)
    {
        $this->confirms->removeElement($confirms);
    }

    /**
     * Get confirms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConfirms()
    {
        return $this->confirms;
    }

    /**
     * Add players
     *
     * @param \AppBundle\Entity\User $players
     * @return Game
     */
    public function addPlayer(\AppBundle\Entity\User $players)
    {
        $this->players[] = $players;

        return $this;
    }

    /**
     * Remove players
     *
     * @param \AppBundle\Entity\User $players
     */
    public function removePlayer(\AppBundle\Entity\User $players)
    {
        $this->players->removeElement($players);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlayers()
    {
        return $this->players;
    }
}

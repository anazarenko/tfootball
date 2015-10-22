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
    const STATUS_COMPLETED = 1;
    const STATUS_REJECTED = 2;

    const FORM_SINGLE = 0;
    const FORM_DOUBLE = 1;

    const RESULT_FIRST_WINNER = 1;
    const RESULT_SECOND_WINNER = 2;
    const RESULT_DRAW = 0;

    public $availableStatus = array(
        0 => 'New',
        1 => 'Confirmed',
        2 => 'Rejected'
    );

    public $availableForm = array(
        0 => '1x1',
        1 => '2x2'
    );

    public $availableType = array(
        0 => 'Friendly',
        1 => 'Tournament'
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
    private $form = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type = 0;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="first_player_id", referencedColumnName="id")
     */
    private $firstPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="second_player_id", referencedColumnName="id")
     */
    private $secondPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_winner_id", referencedColumnName="id")
     */
    private $winner;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_loser_id", referencedColumnName="id")
     */
    private $loser;

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
    private $firstGoals;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(value = 0)
     */
    private $secondGoals;

    /**
     * @ORM\Column(type="integer")
     */
    private $confirmedFirst = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $confirmedSecond = 0;

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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="games")
     **/
    private $players;

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
     * Set firstGoals
     *
     * @param integer $firstGoals
     * @return Game
     */
    public function setFirstGoals($firstGoals)
    {
        $this->firstGoals = $firstGoals;

        return $this;
    }

    /**
     * Get firstGoals
     *
     * @return integer 
     */
    public function getFirstGoals()
    {
        return $this->firstGoals;
    }

    /**
     * Set secondGoals
     *
     * @param integer $secondGoals
     * @return Game
     */
    public function setSecondGoals($secondGoals)
    {
        $this->secondGoals = $secondGoals;

        return $this;
    }

    /**
     * Get secondGoals
     *
     * @return integer 
     */
    public function getSecondGoals()
    {
        return $this->secondGoals;
    }

    /**
     * Set confirmedFirst
     *
     * @param integer $confirmedFirst
     * @return Game
     */
    public function setConfirmedFirst($confirmedFirst)
    {
        $this->confirmedFirst = $confirmedFirst;

        return $this;
    }

    /**
     * Get confirmedFirst
     *
     * @return integer 
     */
    public function getConfirmedFirst()
    {
        return $this->confirmedFirst;
    }

    /**
     * Set confirmedSecond
     *
     * @param integer $confirmedSecond
     * @return Game
     */
    public function setConfirmedSecond($confirmedSecond)
    {
        $this->confirmedSecond = $confirmedSecond;

        return $this;
    }

    /**
     * Get confirmedSecond
     *
     * @return integer 
     */
    public function getConfirmedSecond()
    {
        return $this->confirmedSecond;
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
     * Set firstPlayer
     *
     * @param \AppBundle\Entity\User $firstPlayer
     * @return Game
     */
    public function setFirstPlayer(\AppBundle\Entity\User $firstPlayer = null)
    {
        $this->firstPlayer = $firstPlayer;

        return $this;
    }

    /**
     * Get firstPlayer
     *
     * @return \AppBundle\Entity\User 
     */
    public function getFirstPlayer()
    {
        return $this->firstPlayer;
    }

    /**
     * Set secondPlayer
     *
     * @param \AppBundle\Entity\User $secondPlayer
     * @return Game
     */
    public function setSecondPlayer(\AppBundle\Entity\User $secondPlayer = null)
    {
        $this->secondPlayer = $secondPlayer;

        return $this;
    }

    /**
     * Get secondPlayer
     *
     * @return \AppBundle\Entity\User 
     */
    public function getSecondPlayer()
    {
        return $this->secondPlayer;
    }

    /**
     * Set winner
     *
     * @param \AppBundle\Entity\User $winner
     * @return Game
     */
    public function setWinner(\AppBundle\Entity\User $winner = null)
    {
        $this->winner = $winner;

        return $this;
    }

    /**
     * Get winner
     *
     * @return \AppBundle\Entity\User 
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * Set loser
     *
     * @param \AppBundle\Entity\User $loser
     * @return Game
     */
    public function setLoser(\AppBundle\Entity\User $loser = null)
    {
        $this->loser = $loser;

        return $this;
    }

    /**
     * Get loser
     *
     * @return \AppBundle\Entity\User 
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

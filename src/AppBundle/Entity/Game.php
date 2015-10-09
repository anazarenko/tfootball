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

    const FORM_SINGLE = 0;
    const FORM_DOUBLE = 1;

    public $availableStatus = array(
        0 => 'New',
        1 => 'Completed'
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="xFirstPlayers")
     * @ORM\JoinColumn(name="user_x_first_id", referencedColumnName="id")
     */
    private $xFirstPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="xSecondPlayers")
     * @ORM\JoinColumn(name="user_x_second_id", referencedColumnName="id")
     */
    private $xSecondPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="yFirstPlayers")
     * @ORM\JoinColumn(name="user_y_first_id", referencedColumnName="id")
     */
    private $yFirstPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="ySecondPlayers")
     * @ORM\JoinColumn(name="user_y_second_id", referencedColumnName="id")
     */
    private $ySecondPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="wonGames")
     * @ORM\JoinColumn(name="user_winner_id", referencedColumnName="id")
     */
    private $winner;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="drawnGames")
     **/
    private $drawn;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="lostGames")
     * @ORM\JoinColumn(name="user_loser_id", referencedColumnName="id")
     */
    private $loser;

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
    }

    public function __construct() {
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
        $this->drawn = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set xFirstPlayer
     *
     * @param \AppBundle\Entity\User $xFirstPlayer
     * @return Game
     */
    public function setXFirstPlayer(\AppBundle\Entity\User $xFirstPlayer = null)
    {
        $this->xFirstPlayer = $xFirstPlayer;

        return $this;
    }

    /**
     * Get xFirstPlayer
     *
     * @return \AppBundle\Entity\User 
     */
    public function getXFirstPlayer()
    {
        return $this->xFirstPlayer;
    }

    /**
     * Set xSecondPlayer
     *
     * @param \AppBundle\Entity\User $xSecondPlayer
     * @return Game
     */
    public function setXSecondPlayer(\AppBundle\Entity\User $xSecondPlayer = null)
    {
        $this->xSecondPlayer = $xSecondPlayer;

        return $this;
    }

    /**
     * Get xSecondPlayer
     *
     * @return \AppBundle\Entity\User 
     */
    public function getXSecondPlayer()
    {
        return $this->xSecondPlayer;
    }

    /**
     * Set yFirstPlayer
     *
     * @param \AppBundle\Entity\User $yFirstPlayer
     * @return Game
     */
    public function setYFirstPlayer(\AppBundle\Entity\User $yFirstPlayer = null)
    {
        $this->yFirstPlayer = $yFirstPlayer;

        return $this;
    }

    /**
     * Get yFirstPlayer
     *
     * @return \AppBundle\Entity\User 
     */
    public function getYFirstPlayer()
    {
        return $this->yFirstPlayer;
    }

    /**
     * Set ySecondPlayer
     *
     * @param \AppBundle\Entity\User $ySecondPlayer
     * @return Game
     */
    public function setYSecondPlayer(\AppBundle\Entity\User $ySecondPlayer = null)
    {
        $this->ySecondPlayer = $ySecondPlayer;

        return $this;
    }

    /**
     * Get ySecondPlayer
     *
     * @return \AppBundle\Entity\User 
     */
    public function getYSecondPlayer()
    {
        return $this->ySecondPlayer;
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
     * Add drawn
     *
     * @param \AppBundle\Entity\User $drawn
     * @return Game
     */
    public function addDrawn(\AppBundle\Entity\User $drawn)
    {
        $this->drawn[] = $drawn;

        return $this;
    }

    /**
     * Remove drawn
     *
     * @param \AppBundle\Entity\User $drawn
     */
    public function removeDrawn(\AppBundle\Entity\User $drawn)
    {
        $this->drawn->removeElement($drawn);
    }

    /**
     * Get drawn
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDrawn()
    {
        return $this->drawn;
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

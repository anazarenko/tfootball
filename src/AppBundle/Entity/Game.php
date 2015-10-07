<?php

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Game
 *
 * @ORM\Table(name="games")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\GameRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Game
{

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="firstPositions")
     * @ORM\JoinColumn(name="user_first_id", referencedColumnName="id")
     */
    private $firstPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="secondPositions")
     * @ORM\JoinColumn(name="user_second_id", referencedColumnName="id")
     */
    private $secondPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="wonGames")
     * @ORM\JoinColumn(name="user_winner_id", referencedColumnName="id")
     */
    private $winner;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $drawn = 0;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="lostGames")
     * @ORM\JoinColumn(name="user_loser_id", referencedColumnName="id")
     */
    private $loser;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $firstGoals;

    /**
     * @ORM\Column(type="integer", nullable=true)
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
    public function setFirstPlayer($firstPlayer)
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
    public function setSecondPlayer($secondPlayer)
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
    public function setWinner($winner)
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
    public function setLoser($loser)
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

    /**
     * Set drawn
     *
     * @param integer $drawn
     * @return Game
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
}

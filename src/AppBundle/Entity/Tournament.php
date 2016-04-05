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
    const FORM_SINGLE = 0;
    const FORM_DOUBLE = 1;

    const STATUS_PROGRESS = 0;
    const STATUS_REJECTED = 1;
    const STATUS_FINISHED = 2;

    public $availableForm = array(
        self::FORM_SINGLE => 'Single',
        self::FORM_DOUBLE => 'Double'
    );

    public $availableStatus = array(
        self::STATUS_PROGRESS => 'Progress',
        self::STATUS_REJECTED => 'Canceled',
        self::STATUS_FINISHED => 'Finished'
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_creator_id", referencedColumnName="id")
     */
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Game", mappedBy="tournament")
     */
    private $games;

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
}

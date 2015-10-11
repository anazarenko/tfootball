<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, \Serializable
{

    public $availableRoles = array(
        0 => 'ROLE_ADMIN',
        1 => 'ROLE_USER'
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
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Game", mappedBy="winner")
     **/
    private $wonGames;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Game", inversedBy="drawn")
     * @ORM\JoinTable(name="drawn_games")
     **/
    private $drawnGames;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Game", mappedBy="loser")
     **/
    private $lostGames;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Game", mappedBy="creator")
     **/
    private $createdGames;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Game", inversedBy="players")
     * @ORM\JoinTable(name="players_games")
     **/
    private $games;

    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));

        $this->games = new \Doctrine\Common\Collections\ArrayCollection();
        $this->drawnGames = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
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
     * @return User
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add wonGames
     *
     * @param \AppBundle\Entity\Game $wonGames
     * @return User
     */
    public function addWonGame(\AppBundle\Entity\Game $wonGames)
    {
        $this->wonGames[] = $wonGames;

        return $this;
    }

    /**
     * Remove wonGames
     *
     * @param \AppBundle\Entity\Game $wonGames
     */
    public function removeWonGame(\AppBundle\Entity\Game $wonGames)
    {
        $this->wonGames->removeElement($wonGames);
    }

    /**
     * Get wonGames
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWonGames($status = null)
    {
        if ($status) {
            return $this->wonGames->filter(function($game) use ($status) {
                /** @var \AppBundle\Entity\Game $game */
                return $game->getStatus() == $status;
            });
        } else {
            return $this->wonGames;
        }
    }

    /**
     * Add drawnGames
     *
     * @param \AppBundle\Entity\Game $drawnGames
     * @return User
     */
    public function addDrawnGame(\AppBundle\Entity\Game $drawnGames)
    {
        $this->drawnGames[] = $drawnGames;

        return $this;
    }

    /**
     * Remove drawnGames
     *
     * @param \AppBundle\Entity\Game $drawnGames
     */
    public function removeDrawnGame(\AppBundle\Entity\Game $drawnGames)
    {
        $this->drawnGames->removeElement($drawnGames);
    }

    /**
     * Get drawnGames
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDrawnGames($status = null)
    {
        if ($status) {
            return $this->drawnGames->filter(function($game) use ($status) {
                /** @var \AppBundle\Entity\Game $game */
                return $game->getStatus() == $status;
            });
        } else {
            return $this->drawnGames;
        }
    }

    /**
     * Add lostGames
     *
     * @param \AppBundle\Entity\Game $lostGames
     * @return User
     */
    public function addLostGame(\AppBundle\Entity\Game $lostGames)
    {
        $this->lostGames[] = $lostGames;

        return $this;
    }

    /**
     * Remove lostGames
     *
     * @param \AppBundle\Entity\Game $lostGames
     */
    public function removeLostGame(\AppBundle\Entity\Game $lostGames)
    {
        $this->lostGames->removeElement($lostGames);
    }

    /**
     * Get lostGames
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLostGames($status = null)
    {
        if ($status) {
            return $this->lostGames->filter(function($game) use ($status) {
                /** @var \AppBundle\Entity\Game $game */
                return $game->getStatus() == $status;
            });
        } else {
            return $this->lostGames;
        }
    }

    /**
     * Add games
     *
     * @param \AppBundle\Entity\Game $games
     * @return User
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
    public function getGames($status = null)
    {
        if ($status) {
            return $this->games->filter(function($game) use ($status) {
                /** @var \AppBundle\Entity\Game $game */
                return $game->getStatus() == $status;
            });
        } else {
            return $this->games;
        }
    }

    /**
     * Add createdGames
     *
     * @param \AppBundle\Entity\Game $createdGames
     * @return User
     */
    public function addCreatedGame(\AppBundle\Entity\Game $createdGames)
    {
        $this->createdGames[] = $createdGames;

        return $this;
    }

    /**
     * Remove createdGames
     *
     * @param \AppBundle\Entity\Game $createdGames
     */
    public function removeCreatedGame(\AppBundle\Entity\Game $createdGames)
    {
        $this->createdGames->removeElement($createdGames);
    }

    /**
     * Get createdGames
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCreatedGames()
    {
        return $this->createdGames;
    }
}

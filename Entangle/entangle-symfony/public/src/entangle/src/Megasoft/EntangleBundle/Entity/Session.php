<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sessio
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Session {

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
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="sessionId", type="string", length=255)
     */
    private $sessionId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var boolean
     *
     * @ORM\Column(name="expired", type="boolean")
     */
    private $expired;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceType", type="string")
     */
    private $deviceType;

    /**
     * @var string
     *
     * @ORM\Column(name="regId", type="string")
     */
    private $regId;

    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sessions")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Session
     */
    public function setUserId($usedId) {
        $this->userId = $usedId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     * @return Sessio
     */
    public function setSessionId($sessionId) {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId() {
        return $this->sessionId;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Session
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     * @return Session
     */
    public function setExpired($expired) {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean 
     */
    public function getExpired() {
        return $this->expired;
    }

    /**
     * Set user
     *
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @return Session
     */
    public function setUser(\Megasoft\EntangleBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Megasoft\EntangleBundle\Entity\User 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set deviceType
     *
     * @param string $deviceType
     * @return Session
     */
    public function setDeviceType($deviceType) {
        $this->deviceType = $deviceType;

        return $this;
    }

    /**
     * Get deviceType
     *
     * @return string 
     */
    public function getDeviceType() {
        return $this->deviceType;
    }

    /**
     * Set regId
     *
     * @param string $regId
     * @return Session
     */
    public function setRegId($regId) {
        $this->regId = $regId;

        return $this;
    }

    /**
     * Get regId
     *
     * @return string 
     */
    public function getRegId() {
        return $this->regId;
    }

}

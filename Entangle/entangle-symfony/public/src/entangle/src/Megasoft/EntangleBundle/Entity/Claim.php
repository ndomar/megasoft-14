<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Claim
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Claim
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
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="usedId", type="integer")
     */
    private $usedId;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="tangleId", type="integer")
     */
    private $tangleId;
    
    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="claims")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;
    
    /**
     *
     * @var Tangle
     * 
     * @ORM\ManyToOne(targetEntity="Tangle", inversedBy="claims")
     * @ORM\JoinColumn(name="tangleId", referencedColumnName="id")
     */
    private $tangle;
    
    
    

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
     * Set message
     *
     * @param string $message
     * @return Claim
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Claim
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
     * Set usedId
     *
     * @param integer $usedId
     * @return Claim
     */
    public function setUsedId($usedId)
    {
        $this->usedId = $usedId;

        return $this;
    }

    /**
     * Get usedId
     *
     * @return integer 
     */
    public function getUsedId()
    {
        return $this->usedId;
    }

    /**
     * Set tangleId
     *
     * @param integer $tangleId
     * @return Claim
     */
    public function setTangleId($tangleId)
    {
        $this->tangleId = $tangleId;

        return $this;
    }

    /**
     * Get tangleId
     *
     * @return integer 
     */
    public function getTangleId()
    {
        return $this->tangleId;
    }

    /**
     * Set user
     *
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @return Claim
     */
    public function setUser(\Megasoft\EntangleBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Megasoft\EntangleBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set tangle
     *
     * @param \Megasoft\EntangleBundle\Entity\Tangle $tangle
     * @return Claim
     */
    public function setTangle(\Megasoft\EntangleBundle\Entity\Tangle $tangle = null)
    {
        $this->tangle = $tangle;

        return $this;
    }

    /**
     * Get tangle
     *
     * @return \Megasoft\EntangleBundle\Entity\Tangle 
     */
    public function getTangle()
    {
        return $this->tangle;
    }
}

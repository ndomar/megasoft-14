<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InvitationCode
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class InvitationCode
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
     * @var integer
     *
     * @ORM\Column(name="inviterId", type="integer")
     */
    private $inviterId;
    
     /**
     * @var integer
     *
     * @ORM\Column(name="tangleId", type="integer")
     */
    private $tangleId;
    
    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitations")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $inviter;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

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
     * @var integer
     *
     * @ORM\Column(name="userId", type="integer" ,nullable=true)
     */
    private $userId;
    
    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitationCodes")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;
    
    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Tangle")
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
     * Set inviterId
     *
     * @param integer $inviterId
     * @return InvitationCode
     */
    public function setInviterId($inviterId)
    {
        $this->inviterId = $inviterId;

        return $this;
    }

    /**
     * Get inviterId
     *
     * @return integer 
     */
    public function getInviterId()
    {
        return $this->inviterId;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return InvitationCode
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return InvitationCode
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     * @return InvitationCode
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean 
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return InvitationCode
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return InvitationCode
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
     * Set inviter
     *
     * @param \Megasoft\EntangleBundle\Entity\User $inviter
     * @return InvitationCode
     */
    public function setInviter(\Megasoft\EntangleBundle\Entity\User $inviter = null)
    {
        $this->inviter = $inviter;

        return $this;
    }

    /**
     * Get inviter
     *
     * @return \Megasoft\EntangleBundle\Entity\User 
     */
    public function getInviter()
    {
        return $this->inviter;
    }

    /**
     * Set user
     *
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @return InvitationCode
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
     * Set tangleId
     *
     * @param integer $tangleId
     * @return InvitationCode
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
     * Set tangle
     *
     * @param \Megasoft\EntangleBundle\Entity\Tangle $tangle
     * @return InvitationCode
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

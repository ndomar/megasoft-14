<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PendingInvitation
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PendingInvitation
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
     * @ORM\Column(name="inviteeId", type="integer",nullable=true)
     */
    private $inviteeId;

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
     * @var integer
     *
     * @ORM\Column(name="messageId", type="integer")
     */
    private $messageId;

    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="pendingInvitationInviters")
     * @ORM\JoinColumn(name="inviterId", referencedColumnName="id")
     */
    private $inviter;
    
    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="pendingInvitationInvitees")
     * @ORM\JoinColumn(name="inviteeId", referencedColumnName="id")
     */
    private $invitee;
    
    /**
     *
     * @var Tangle
     * 
     * @ORM\ManyToOne(targetEntity="Tangle", inversedBy="pendingInvitations")
     * @ORM\JoinColumn(name="tangleId", referencedColumnName="id")
     */
    private $tangle;
    
    /**
     *
     * @var InvitationMessage
     * 
     * @ORM\ManyToOne(targetEntity="InvitationMessage", inversedBy="pendingInvitations")
     * @ORM\JoinColumn(name="messageId", referencedColumnName="id")
     */
    private $message;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;
    
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
     * Set inviteeId
     *
     * @param integer $inviteeId
     * @return PendingInvitation
     */
    public function setInviteeId($inviteeId)
    {
        $this->inviteeId = $inviteeId;

        return $this;
    }

    /**
     * Get inviteeId
     *
     * @return integer 
     */
    public function getInviteeId()
    {
        return $this->inviteeId;
    }

    /**
     * Set inviterId
     *
     * @param integer $inviterId
     * @return PendingInvitation
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
     * Set tangleId
     *
     * @param integer $tangleId
     * @return PendingInvitation
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
     * Set messageId
     *
     * @param integer $messageId
     * @return PendingInvitation
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * Get messageId
     *
     * @return integer 
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * Set inviter
     *
     * @param \Megasoft\EntangleBundle\Entity\User $inviter
     * @return PendingInvitation
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
     * Set invitee
     *
     * @param \Megasoft\EntangleBundle\Entity\User $invitee
     * @return PendingInvitation
     */
    public function setInvitee(\Megasoft\EntangleBundle\Entity\User $invitee = null)
    {
        $this->invitee = $invitee;

        return $this;
    }

    /**
     * Get invitee
     *
     * @return \Megasoft\EntangleBundle\Entity\User 
     */
    public function getInvitee()
    {
        return $this->invitee;
    }

    /**
     * Set tangle
     *
     * @param \Megasoft\EntangleBundle\Entity\Tangle $tangle
     * @return PendingInvitation
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

    /**
     * Set message
     *
     * @param \Megasoft\EntangleBundle\Entity\InvitationMessage $message
     * @return PendingInvitation
     */
    public function setMessage(\Megasoft\EntangleBundle\Entity\InvitationMessage $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \Megasoft\EntangleBundle\Entity\InvitationMessage 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return PendingInvitation
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
}

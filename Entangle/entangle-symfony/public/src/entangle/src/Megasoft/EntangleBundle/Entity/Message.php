<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Message
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
     *
     * @var string
     * 
     * @ORM\Column(name="body", type="string")
     */
    private $body;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var Notification[]
     * 
     * @ORM\OneToMany(targetEntity="NewMessageNotification", mappedBy="message", cascade={"persist"})
     */
    private $notifications;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="senderId", type="integer")
     */
    private $senderId;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="offerId", type="integer")
     */
    private $offerId;
    
    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messages")
     * @ORM\JoinColumn(name="senderId", referencedColumnName="id")
     */
    private $sender;
    
    /**
     *
     * @var Offer
     * 
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="messages")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    private $offer;
    
    /**
     *
     * @var boolean
     * 
     * @ORM\Column(name="deleted", type="boolean" , columnDefinition="tinyint(1) DEFAULT 0")
     */
    private $deleted = false;
    
    
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
     * Set date
     *
     * @param \DateTime $date
     * @return Message
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set senderId
     *
     * @param integer $senderId
     * @return Message
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;

        return $this;
    }

    /**
     * Get senderId
     *
     * @return integer 
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * Set offerId
     *
     * @param integer $offerId
     * @return Message
     */
    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;

        return $this;
    }

    /**
     * Get offerId
     *
     * @return integer 
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     * Add notifications
     *
     * @param \Megasoft\EntangleBundle\Entity\NewMessageNotification $notifications
     * @return Message
     */
    public function addNotification(\Megasoft\EntangleBundle\Entity\NewMessageNotification $notifications)
    {
        $this->notifications[] = $notifications;
        $notifications->setMessage($this);
        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \Megasoft\EntangleBundle\Entity\NewMessageNotification $notifications
     */
    public function removeNotification(\Megasoft\EntangleBundle\Entity\NewMessageNotification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set sender
     *
     * @param \Megasoft\EntangleBundle\Entity\User $sender
     * @return Message
     */
    public function setSender(\Megasoft\EntangleBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Megasoft\EntangleBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set offer
     *
     * @param \Megasoft\EntangleBundle\Entity\Offer $offer
     * @return Message
     */
    public function setOffer(\Megasoft\EntangleBundle\Entity\Offer $offer = null)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Get offer
     *
     * @return \Megasoft\EntangleBundle\Entity\Offer 
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Message
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }
}

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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var Notification[]
     * 
     * @ORM\OneToMany(targetEntity="NewMessageNotification", mappedBy="message")
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
}

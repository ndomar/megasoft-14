<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class NewMessageNotification extends Notification
{
    /**
     * @var integer
     *
     * @ORM\Column(name="messageId", type="integer")
     */
    private $messageId;
    
    
    /**
     *
     * @var Message
     * 
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="notifications")
     * @ORM\JoinColumn(name="messageId", referencedColumnName="id")
     */
    private $message;
    

    /**
     * Set messageId
     *
     * @param integer $messageId
     * @return NewMessageNotification
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
     * Set message
     *
     * @param \Megasoft\EntangleBundle\Entity\Message $message
     * @return NewMessageNotification
     */
    public function setMessage(\Megasoft\EntangleBundle\Entity\Message $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \Megasoft\EntangleBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }
}

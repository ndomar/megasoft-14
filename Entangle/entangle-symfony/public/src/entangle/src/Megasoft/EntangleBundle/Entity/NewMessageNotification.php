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
    
}

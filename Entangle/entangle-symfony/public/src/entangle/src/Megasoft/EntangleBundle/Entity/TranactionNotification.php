<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TransactionNotification extends Notification
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="transactionId", type="integer")
     */
    private $transactionId;
    
    /**
     *
     * @var Transaction
     * 
     * @ORM\ManyToOne(targetEntity="Transaction", inversedBy="notifications")
     * @ORM\JoinColumn(name="tranactionId", referencedColumnName="id")
     */
    private $transaction;
}

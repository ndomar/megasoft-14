<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PriceChangeNotification extends Notification
{
   /**
     * @var integer
     *
     * @ORM\Column(name="oldPrice", type="integer")
     */
    private $oldPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="newPrice", type="integer")
     */
    private $newPrice;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="requestId", type="integer")
     */
    private $requestId;
    
    /**
     *
     * @var Request
     * 
     * @ORM\ManyToOne(targetEntity="Request", inversedBy="notifications")
     * @ORM\JoinColumn(name="requestId", referencedColumnName="id")
     */
    private $request;
}

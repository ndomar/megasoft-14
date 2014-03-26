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

    /**
     * Set oldPrice
     *
     * @param integer $oldPrice
     * @return PriceChangeNotification
     */
    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    /**
     * Get oldPrice
     *
     * @return integer 
     */
    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    /**
     * Set newPrice
     *
     * @param integer $newPrice
     * @return PriceChangeNotification
     */
    public function setNewPrice($newPrice)
    {
        $this->newPrice = $newPrice;

        return $this;
    }

    /**
     * Get newPrice
     *
     * @return integer 
     */
    public function getNewPrice()
    {
        return $this->newPrice;
    }

    /**
     * Set requestId
     *
     * @param integer $requestId
     * @return PriceChangeNotification
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Get requestId
     *
     * @return integer 
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set request
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $request
     * @return PriceChangeNotification
     */
    public function setRequest(\Megasoft\EntangleBundle\Entity\Request $request = null)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return \Megasoft\EntangleBundle\Entity\Request 
     */
    public function getRequest()
    {
        return $this->request;
    }
}

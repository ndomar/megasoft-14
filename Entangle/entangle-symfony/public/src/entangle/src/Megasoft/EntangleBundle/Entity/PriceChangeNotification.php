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
     * @ORM\Column(name="priceChangeOfferId", type="integer")
     */
    private $offerId;
    
    /**
     *
     * @var Offer
     * 
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="notifications")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    private $offer;

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
     * Set offerId
     *
     * @param integer $offerId
     * @return PriceChangeNotification
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
     * Set offer
     *
     * @param \Megasoft\EntangleBundle\Entity\Offer $offer
     * @return PriceChangeNotification
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
}

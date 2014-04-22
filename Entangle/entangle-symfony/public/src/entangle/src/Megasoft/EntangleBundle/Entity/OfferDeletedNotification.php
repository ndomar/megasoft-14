<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OfferDeletedNotification
 *
 * @ORM\Entity
 */
class OfferDeletedNotification extends Notification
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="deletedOfferId", type="integer")
     */
    private $offerId;
    
    /**
     *
     * @var Offer
     * 
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="offerDeletedNotifications")
     * @ORM\JoinColumn(name="deletedOfferId", referencedColumnName="id")
     */
    private $offer;


    /**
     * Set offerId
     *
     * @param integer $offerId
     * @return OfferDeletedNotification
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
     * @return OfferDeletedNotification
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

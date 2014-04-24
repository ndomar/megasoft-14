<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OfferChosenNotification
 *
 * @ORM\Entity
 */
class OfferChosenNotification extends Notification
{

    /**
     * @var integer
     *
     * @ORM\Column(name="chosenOfferId", type="integer")
     */
    private $offerId;
    
    /**
     *
     * @var Offer
     * 
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="offerChosenNotifications")
     * @ORM\JoinColumn(name="chosenOfferId", referencedColumnName="id")
     */
    private $offer;

    /**
     * Set offerId
     *
     * @param integer $offerId
     * @return OfferChosenNotification
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
     * @return OfferChosenNotification
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

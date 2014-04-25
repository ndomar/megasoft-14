<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class NewOfferNotification extends Notification
{
    /**
     * @var integer
     * 
     * @ORM\Column(name="newOfferId", type="integer")
     */
    private $offerId;

    /**
     * @var \Megasoft\EntangleBundle\Entity\Offer
     * 
     * @ORM\ManyToOne(targetEntity="Offer")
     * @ORM\JoinColumn(name="newOfferId", referencedColumnName="id")
     */
    private $offer;

    /**
     * Set offerId
     *
     * @param integer $offerId
     * @return NewOfferNotification
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
     * @return NewOfferNotification
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

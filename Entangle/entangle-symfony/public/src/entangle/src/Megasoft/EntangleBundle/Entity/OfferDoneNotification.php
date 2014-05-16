<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OfferDoneNotification extends Notification
{
    /**
     * @var integer
     *
     * @ORM\Column(name="doneOfferId", type="integer")
     */
    private $offerId;

    /**
     * @var \Megasoft\EntangleBundle\Entity\Offer
     *
     * @ORM\ManyToOne(targetEntity="Offer")
     * @ORM\JoinColumn(name="doneOfferId", referencedColumnName="id")
     */
    private $offer;

    /**
     * Set offerId
     *
     * @param integer $offerId
     * @return OfferDoneNotification
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
     * @return OfferDoneNotification
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

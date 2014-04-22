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
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
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
}

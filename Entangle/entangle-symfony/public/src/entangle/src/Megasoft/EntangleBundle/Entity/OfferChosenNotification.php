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
     * @ORM\Column(name="ChosenOfferId", type="integer")
     */
    private $offerId;

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
}

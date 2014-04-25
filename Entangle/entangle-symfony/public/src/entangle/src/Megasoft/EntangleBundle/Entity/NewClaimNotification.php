<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class NewClaimNotification extends Notification
{
    /**
     * @var integer
     * 
     * @ORM\Column(name="newClaimId", type="integer")
     */
    private $claimId;

    /**
     * @var \Megasoft\EntangleBundle\Entity\Claim
     * 
     * @ORM\ManyToOne(targetEntity="Claim")
     * @ORM\JoinColumn(name="newClaimId", referencedColumnName="id")
     */
    private $claim;

    /**
     * Set claimId
     *
     * @param integer $claimId
     * @return NewClaimNotification
     */
    public function setClaimId($claimId)
    {
        $this->claimId = $claimId;

        return $this;
    }

    /**
     * Get claimId
     *
     * @return integer 
     */
    public function getClaimId()
    {
        return $this->claimId;
    }

    /**
     * Set claim
     *
     * @param \Megasoft\EntangleBundle\Entity\Claim $claim
     * @return NewClaimNotification
     */
    public function setClaim(\Megasoft\EntangleBundle\Entity\Claim $claim = null)
    {
        $this->claim = $claim;

        return $this;
    }

    /**
     * Get claim
     *
     * @return \Megasoft\EntangleBundle\Entity\Claim 
     */
    public function getClaim()
    {
        return $this->claim;
    }
}

<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Claim
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Claim
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="claimerId", type="integer")
     */
    private $claimerId;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="offerId", type="integer")
     */
    private $offerId;
    
    /**
     *
     * @var Offer
     * 
     * @ORM\ManyToOne(targetEntity="Offer")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    private $offer;
    
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="tangleId", type="integer")
     */
    private $tangleId;
    
    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="claims")
     * @ORM\JoinColumn(name="claimerId", referencedColumnName="id")
     */
    private $claimer;
    
    /**
     *
     * @var Tangle
     * 
     * @ORM\ManyToOne(targetEntity="Tangle", inversedBy="claims")
     * @ORM\JoinColumn(name="tangleId", referencedColumnName="id")
     */
    private $tangle;
    
    /**
     *
     * @var boolean
     * 
     * @ORM\Column(name="deleted", type="boolean" , columnDefinition="tinyint(1) DEFAULT 0")
     */
    private $deleted = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;
     

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Claim
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Claim
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set tangleId
     *
     * @param integer $tangleId
     * @return Claim
     */
    public function setTangleId($tangleId)
    {
        $this->tangleId = $tangleId;

        return $this;
    }

    /**
     * Get tangleId
     *
     * @return integer 
     */
    public function getTangleId()
    {
        return $this->tangleId;
    }

    /**
     * Set tangle
     *
     * @param \Megasoft\EntangleBundle\Entity\Tangle $tangle
     * @return Claim
     */
    public function setTangle(\Megasoft\EntangleBundle\Entity\Tangle $tangle = null)
    {
        $this->tangle = $tangle;

        return $this;
    }

    /**
     * Get tangle
     *
     * @return \Megasoft\EntangleBundle\Entity\Tangle 
     */
    public function getTangle()
    {
        return $this->tangle;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Claim
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set claimerId
     *
     * @param integer $claimerId
     * @return Claim
     */
    public function setClaimerId($claimerId)
    {
        $this->claimerId = $claimerId;

        return $this;
    }

    /**
     * Get claimerId
     *
     * @return integer 
     */
    public function getClaimerId()
    {
        return $this->claimerId;
    }

    /**
     * Set offerId
     *
     * @param integer $offerId
     * @return Claim
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
     * @return Claim
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

    /**
     * Set claimer
     *
     * @param \Megasoft\EntangleBundle\Entity\User $claimer
     * @return Claim
     */
    public function setClaimer(\Megasoft\EntangleBundle\Entity\User $claimer = null)
    {
        $this->claimer = $claimer;

        return $this;
    }

    /**
     * Get claimer
     *
     * @return \Megasoft\EntangleBundle\Entity\User 
     */
    public function getClaimer()
    {
        return $this->claimer;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Claim
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }
}

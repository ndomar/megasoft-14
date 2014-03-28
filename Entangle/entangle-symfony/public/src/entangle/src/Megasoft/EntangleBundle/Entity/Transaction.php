<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Transaction
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="offerId", type="integer")
     */
    private $offerId;
    
    /**
     * @ORM\OneToOne(targetEntity="Offer")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    private $offer;
    
    
    /**
     * @var Notification[]
     * 
     * @ORM\OneToMany(targetEntity="TransactionNotification", mappedBy="transaction", cascade={"persist"})
     */
    private $notifications;
    

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
     * Set date
     *
     * @param \DateTime $date
     * @return Transaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set offerId
     *
     * @param integer $offerId
     * @return Transaction
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
     * @return Transaction
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
     * Add notifications
     *
     * @param \Megasoft\EntangleBundle\Entity\TransactionNotification $notifications
     * @return Transaction
     */
    public function addNotification(\Megasoft\EntangleBundle\Entity\TransactionNotification $notifications)
    {
        $this->notifications[] = $notifications;
        $notifications->setTransaction($this);
        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \Megasoft\EntangleBundle\Entity\TransactionNotification $notifications
     */
    public function removeNotification(\Megasoft\EntangleBundle\Entity\TransactionNotification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}

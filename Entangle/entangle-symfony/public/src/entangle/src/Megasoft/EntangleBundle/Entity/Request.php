<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Request
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Request
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
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="date" , nullable=true)
     */
    private $deadline;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255 , nullable=true)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="requestedPrice", type="integer", nullable=true)
     */
    private $requestedPrice;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="tangleId", type="integer")
     */
    private $tangleId;
    
    /**
     *
     * @var Tangle
     * 
     * @ORM\ManyToOne(targetEntity="Tangle", inversedBy="requests")
     * @ORM\JoinColumn(name="tangleId", referencedColumnName="id")
     */
    private $tangle;
    
    /**
     * @var Offer[]
     * 
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="request", cascade={"persist"})
     */
    private $offers;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;
    
    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requests")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;
    
     /**
      * @var Tag[]
      *
      * @ORM\ManyToMany(targetEntity="Tag", inversedBy="requests", cascade={"persist"})
      * @ORM\JoinTable(name="request_tag")
      */
    private $tags;
    
    /**
     *
     * @var boolean
     * 
     * @ORM\Column(name="deleted", type="boolean" , columnDefinition="tinyint(1) DEFAULT 0")
     */
    private $deleted = false;
    
    /**
     * @var RequestDeletedNotification[]
     * 
     * @ORM\OneToMany(targetEntity="RequestDeletedNotification", mappedBy="request", cascade={"persist"})
     */
    private $requestDeletedNotifications;
    

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
     * Set status
     *
     * @param integer $status
     * @return Request
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
     * Set description
     *
     * @param string $description
     * @return Request
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Request
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
     * Set deadline
     *
     * @param \DateTime $deadline
     * @return Request
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime 
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return Request
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set requestedPrice
     *
     * @param string $requestedPrice
     * @return Request
     */
    public function setRequestedPrice($requestedPrice)
    {
        $this->requestedPrice = $requestedPrice;

        return $this;
    }

    /**
     * Get requestedPrice
     *
     * @return string 
     */
    public function getRequestedPrice()
    {
        return $this->requestedPrice;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->offers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set tangleId
     *
     * @param integer $tangleId
     * @return Request
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
     * Set userId
     *
     * @param integer $userId
     * @return Request
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set tangle
     *
     * @param \Megasoft\EntangleBundle\Entity\Tangle $tangle
     * @return Request
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
     * Add offers
     *
     * @param \Megasoft\EntangleBundle\Entity\Offer $offers
     * @return Request
     */
    public function addOffer(\Megasoft\EntangleBundle\Entity\Offer $offers)
    {
        $this->offers[] = $offers;
        $offers->setRequest($this);
        return $this;
    }

    /**
     * Remove offers
     *
     * @param \Megasoft\EntangleBundle\Entity\Offer $offers
     */
    public function removeOffer(\Megasoft\EntangleBundle\Entity\Offer $offers)
    {
        $this->offers->removeElement($offers);
    }

    /**
     * Get offers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * Set user
     *
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @return Request
     */
    public function setUser(\Megasoft\EntangleBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Megasoft\EntangleBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add tags
     *
     * @param \Megasoft\EntangleBundle\Entity\Tag $tags
     * @return Request
     */
    public function addTag(\Megasoft\EntangleBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
        $tags->addRequest($this);
        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Megasoft\EntangleBundle\Entity\Tag $tags
     */
    public function removeTag(\Megasoft\EntangleBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Request
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
     * Add requestDeletedNotifications
     *
     * @param \Megasoft\EntangleBundle\Entity\RequestDeletedNotification $requestDeletedNotifications
     * @return Request
     */
    public function addRequestDeletedNotification(\Megasoft\EntangleBundle\Entity\RequestDeletedNotification $requestDeletedNotifications)
    {
        $this->requestDeletedNotifications[] = $requestDeletedNotifications;

        return $this;
    }

    /**
     * Remove requestDeletedNotifications
     *
     * @param \Megasoft\EntangleBundle\Entity\RequestDeletedNotification $requestDeletedNotifications
     */
    public function removeRequestDeletedNotification(\Megasoft\EntangleBundle\Entity\RequestDeletedNotification $requestDeletedNotifications)
    {
        $this->requestDeletedNotifications->removeElement($requestDeletedNotifications);
    }

    /**
     * Get requestDeletedNotifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequestDeletedNotifications()
    {
        return $this->requestDeletedNotifications;
    }
}

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
     * @ORM\Column(name="deadline", type="date")
     */
    private $deadline;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="requestedPrice", type="integer")
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
     * @var Notification[]
     * 
     * @ORM\OneToMany(targetEntity="PriceChangeNotification", mappedBy="request")
     */
    private $notifications;
    
    /**
     * @var Offer[]
     * 
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="request")
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
      * @ORM\ManyToMany(targetEntity="Tag", inversedBy="requests")
      * @ORM\JoinTable(name="request_tag")
      */
    private $tags;

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
}

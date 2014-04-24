<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Offer
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Offer {

    const PENDING = 0;
    const ACCEPTED = 1;

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
     * @ORM\Column(name="requestedPrice", type="integer")
     */
    private $requestedPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expectedDeadline", type="date" , nullable=true)
     */
    private $expectedDeadline;

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
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="offers")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;

    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="requestId", type="integer")
     */
    private $requestId;

    /**
     *
     * @var Request
     * 
     * @ORM\ManyToOne(targetEntity="Request", inversedBy="offers")
     * @ORM\JoinColumn(name="requestId", referencedColumnName="id")
     */
    private $request;

    /**
     * @var Message[]
     * 
     * @ORM\OneToMany(targetEntity="Message", mappedBy="offer", cascade={"persist"})
     */
    private $messages;

    /**
     *
     * @var boolean
     * 
     * @ORM\Column(name="deleted", type="boolean" , columnDefinition="tinyint(1) DEFAULT 0")
     */
    private $deleted = false;

    /**
     * @ORM\OneToOne(targetEntity="Transaction", mappedBy="offer")
     */
    private $transaction;

    /**
     * @var PriceChangeNotification[]
     * 
     * @ORM\OneToMany(targetEntity="PriceChangeNotification", mappedBy="offer", cascade={"persist"})
     */
    private $priceChangeNotifications;

    /**
     * @var OfferChosenNotification[]
     * 
     * @ORM\OneToMany(targetEntity="OfferChosenNotification", mappedBy="offer", cascade={"persist"})
     */
    private $offerChosenNotifications;

    /**
     * @var OfferDeletedNotification[]
     * 
     * @ORM\OneToMany(targetEntity="OfferDeletedNotification", mappedBy="offer", cascade={"persist"})
     */
    private $offerDeletedNotifications;

    /**
     *
     * @var integer 
     */
    public $PENDING = 0;

    /**
     *
     * @var integer 
     */
    public $DONE = 1;

    /**
     *
     * @var integer 
     */
    public $ACCEPTED = 2;

    /**
     *
     * @var integer 
     */
    public $FAILED = 3;

    /**
     *
     * @var integer 
     */
    public $REJECTED = 4;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set requestedPrice
     *
     * @param integer $requestedPrice
     * @return Offer
     */
    public function setRequestedPrice($requestedPrice) {
        $this->requestedPrice = $requestedPrice;

        return $this;
    }

    /**
     * Get requestedPrice
     *
     * @return integer 
     */
    public function getRequestedPrice() {
        return $this->requestedPrice;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Offer
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Offer
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set expectedDeadline
     *
     * @param \DateTime $expectedDeadline
     * @return Offer
     */
    public function setExpectedDeadline($expectedDeadline) {
        $this->expectedDeadline = $expectedDeadline;

        return $this;
    }

    /**
     * Get expectedDeadline
     *
     * @return \DateTime 
     */
    public function getExpectedDeadline() {
        return $this->expectedDeadline;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Offer
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set requestId
     *
     * @param integer $requestId
     * @return Offer
     */
    public function setRequestId($requestId) {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Get requestId
     *
     * @return integer 
     */
    public function getRequestId() {
        return $this->requestId;
    }

    /**
     * Set request
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $request
     * @return Offer
     */
    public function setRequest(\Megasoft\EntangleBundle\Entity\Request $request = null) {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return \Megasoft\EntangleBundle\Entity\Request 
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Add messages
     *
     * @param \Megasoft\EntangleBundle\Entity\Message $messages
     * @return Offer
     */
    public function addMessage(\Megasoft\EntangleBundle\Entity\Message $messages) {
        $this->messages[] = $messages;
        $messages->setOffer($this);
        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Megasoft\EntangleBundle\Entity\Message $messages
     */
    public function removeMessage(\Megasoft\EntangleBundle\Entity\Message $messages) {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Offer
     */
    public function setUserId($userId) {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Set user
     *
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @return Offer
     */
    public function setUser(\Megasoft\EntangleBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Megasoft\EntangleBundle\Entity\User 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Offer
     */
    public function setDeleted($deleted) {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted() {
        return $this->deleted;
    }

    /**
     * Set transaction
     *
     * @param \Megasoft\EntangleBundle\Entity\Transaction $transaction
     * @return Offer
     */
    public function setTransaction(\Megasoft\EntangleBundle\Entity\Transaction $transaction = null) {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \Megasoft\EntangleBundle\Entity\Transaction 
     */
    public function getTransaction() {
        return $this->transaction;
    }

    /**
     * Add priceChangeNotifications
     *
     * @param \Megasoft\EntangleBundle\Entity\PriceChangeNotification $priceChangeNotifications
     * @return Offer
     */
    public function addPriceChangeNotification(\Megasoft\EntangleBundle\Entity\PriceChangeNotification $priceChangeNotifications) {
        $this->priceChangeNotifications[] = $priceChangeNotifications;
        $priceChangeNotifications->setOffer($offer);
        return $this;
    }

    /**
     * Remove priceChangeNotifications
     *
     * @param \Megasoft\EntangleBundle\Entity\PriceChangeNotification $priceChangeNotifications
     */
    public function removePriceChangeNotification(\Megasoft\EntangleBundle\Entity\PriceChangeNotification $priceChangeNotifications) {
        $this->priceChangeNotifications->removeElement($priceChangeNotifications);
    }

    /**
     * Get priceChangeNotifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPriceChangeNotifications() {
        return $this->priceChangeNotifications;
    }

    /**
     * Add offerChosenNotifications
     *
     * @param \Megasoft\EntangleBundle\Entity\OfferChosenNotification $offerChosenNotifications
     * @return Offer
     */
    public function addOfferChosenNotification(\Megasoft\EntangleBundle\Entity\OfferChosenNotification $offerChosenNotifications) {
        $this->offerChosenNotifications[] = $offerChosenNotifications;
        $offerChosenNotifications->setOffer($this);
        return $this;
    }

    /**
     * Remove offerChosenNotifications
     *
     * @param \Megasoft\EntangleBundle\Entity\OfferChosenNotification $offerChosenNotifications
     */
    public function removeOfferChosenNotification(\Megasoft\EntangleBundle\Entity\OfferChosenNotification $offerChosenNotifications) {
        $this->offerChosenNotifications->removeElement($offerChosenNotifications);
    }

    /**
     * Get offerChosenNotifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOfferChosenNotifications() {
        return $this->offerChosenNotifications;
    }

    /**
     * Add offerDeletedNotifications
     *
     * @param \Megasoft\EntangleBundle\Entity\OfferDeletedNotification $offerDeletedNotifications
     * @return Offer
     */
    public function addOfferDeletedNotification(\Megasoft\EntangleBundle\Entity\OfferDeletedNotification $offerDeletedNotifications) {
        $this->offerDeletedNotifications[] = $offerDeletedNotifications;
        $offerDeletedNotifications->setOffer($this);
        return $this;
    }

    /**
     * Remove offerDeletedNotifications
     *
     * @param \Megasoft\EntangleBundle\Entity\OfferDeletedNotification $offerDeletedNotifications
     */
    public function removeOfferDeletedNotification(\Megasoft\EntangleBundle\Entity\OfferDeletedNotification $offerDeletedNotifications) {
        $this->offerDeletedNotifications->removeElement($offerDeletedNotifications);
    }

    /**
     * Get offerDeletedNotifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOfferDeletedNotifications() {
        return $this->offerDeletedNotifications;
    }

}

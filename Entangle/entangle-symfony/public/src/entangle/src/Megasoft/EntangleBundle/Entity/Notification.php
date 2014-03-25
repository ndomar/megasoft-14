<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table()
 * @ORM\Entity
 * 
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"transaction" = "TransactionNotification", "price-change" = "PriceChangeNotification", "new-message" = "NewMessageNotification" })
 */
class Notification
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
     * @var boolean
     *
     * @ORM\Column(name="seen", type="boolean")
     */
    private $seen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(name="userId" , type="integer")
     */
    private $userId;

    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notifications")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;
    
    
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
     * Set seen
     *
     * @param boolean $seen
     * @return Notification
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get seen
     *
     * @return boolean 
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Notification
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

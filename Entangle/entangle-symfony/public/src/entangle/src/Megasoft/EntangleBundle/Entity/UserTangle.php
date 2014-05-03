<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserTangle
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserTangle
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
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="tangleId", type="integer")
     */
    private $tangleId;

    /**
     * @var integer
     *
     * @ORM\Column(name="credit", type="integer")
     */
    private $credit;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tangleOwner", type="boolean")
     */
    private $tangleOwner;
    
    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userTangles")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;
    
    /**
     *
     * @var Tangle
     * 
     * @ORM\ManyToOne(targetEntity="Tangle", inversedBy="userTangles")
     * @ORM\JoinColumn(name="tangleId", referencedColumnName="id")
     */
    private $tangle;


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
     * Set userId
     *
     * @param integer $userId
     * @return UserTangle
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
     * Set tangleId
     *
     * @param integer $tangleId
     * @return UserTangle
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
     * Set credit
     *
     * @param integer $credit
     * @return UserTangle
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return integer 
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set tangleOwner
     *
     * @param boolean $tangleOwner
     * @return UserTangle
     */
    public function setTangleOwner($tangleOwner)
    {
        $this->tangleOwner = $tangleOwner;

        return $this;
    }

    /**
     * Get tangleOwner
     *
     * @return boolean 
     */
    public function getTangleOwner()
    {
        return $this->tangleOwner;
    }

    /**
     * Set user
     *
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @return UserTangle
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
     * Set tangle
     *
     * @param \Megasoft\EntangleBundle\Entity\Tangle $tangle
     * @return UserTangle
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
}

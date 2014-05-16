<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ForgetPasswordCode
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ForgetPasswordCode
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
     * @ORM\Column(name="forgetPasswordCode", type="string", length=255)
     */
    private $forgetPasswordCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     *
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var boolean
     *
     * @ORM\Column(name="expired", type="boolean")
     */
    private $expired;


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
     * Set forgetPasswordCode
     *
     * @param string $forgetPasswordCode
     * @return ForgetPasswordCode
     */
    public function setForgetPasswordCode($forgetPasswordCode)
    {
        $this->forgetPasswordCode = $forgetPasswordCode;

        return $this;
    }

    /**
     * Get forgetPasswordCode
     *
     * @return string 
     */
    public function getForgetPasswordCode()
    {
        return $this->forgetPasswordCode;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return ForgetPasswordCode
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
     * Set created
     *
     * @param \DateTime $created
     * @return ForgetPasswordCode
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

    /**
     * Set expired
     *
     * @param boolean $expired
     * @return ForgetPasswordCode
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean 
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set user
     *
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @return ForgetPasswordCode
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
}

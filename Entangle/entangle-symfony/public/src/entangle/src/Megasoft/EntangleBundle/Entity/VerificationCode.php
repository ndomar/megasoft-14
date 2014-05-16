<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VerificationCode
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class VerificationCode
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
     * @ORM\Column(name="verificationCode", type="string", length=255)
     */
    private $verificationCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="userEmailId", type="integer")
     */
    private $userEmailId;
    
    /**
     *
     * @var UserEmail
     * 
     * @ORM\OneToOne(targetEntity="UserEmail", inversedBy="verificationCode")
     * @ORM\JoinColumn(name="userEmailId", referencedColumnName="id")
     */
    private $userEmail;

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
     * Set verificationCode
     *
     * @param string $verificationCode
     * @return VerificationCode
     */
    public function setVerificationCode($verificationCode)
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    /**
     * Get verificationCode
     *
     * @return string 
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return VerificationCode
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
     * @return VerificationCode
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
     * Set userEmailId
     *
     * @param integer $userEmailId
     * @return VerificationCode
     */
    public function setUserEmailId($userEmailId)
    {
        $this->userEmailId = $userEmailId;

        return $this;
    }

    /**
     * Get userEmailId
     *
     * @return integer 
     */
    public function getUserEmailId()
    {
        return $this->userEmailId;
    }

    /**
     * Set userEmail
     *
     * @param \Megasoft\EntangleBundle\Entity\UserEmail $userEmail
     * @return VerificationCode
     */
    public function setUserEmail(\Megasoft\EntangleBundle\Entity\UserEmail $userEmail = null)
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * Get userEmail
     *
     * @return \Megasoft\EntangleBundle\Entity\UserEmail 
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }
}

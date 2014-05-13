<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEmails
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserEmail {

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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     *
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="emails")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;

    /**
     *
     * @var boolean
     * 
     * @ORM\Column(name="verified", type="boolean" , columnDefinition="tinyint(1) DEFAULT 0")
     */
    private $verified = false;

    /**
     *
     * @var boolean
     * 
     * @ORM\Column(name="deleted", type="boolean" , columnDefinition="tinyint(1) DEFAULT 0")
     */
    private $deleted = false;

    /**
     * @var VerificationCode
     *
     * @ORM\OneToOne(targetEntity="VerificationCode", mappedBy="userEmail", cascade={"persist"})
     */
    private $verificationCode;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserEmails
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
     * Set email
     *
     * @param string $email
     * @return UserEmails
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set user
     *
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @return UserEmail
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
     * @return UserEmail
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
     * Set verified
     *
     * @param boolean $verified
     * @return UserEmail
     */
    public function setVerified($verified) {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return boolean 
     */
    public function getVerified() {
        return $this->verified;
    }

    /**
     * Set verificationCode
     *
     * @param \Megasoft\EntangleBundle\Entity\VerificationCode $verificationCode
     * @return UserEmail
     */
    public function setVerificationCode(\Megasoft\EntangleBundle\Entity\VerificationCode $verificationCode = null)
    {
        $this->verificationCode = $verificationCode;
        $verificationCode->setUserEmail($this);
        return $this;
    }

    /**
     * Get verificationCode
     *
     * @return \Megasoft\EntangleBundle\Entity\VerificationCode
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

}

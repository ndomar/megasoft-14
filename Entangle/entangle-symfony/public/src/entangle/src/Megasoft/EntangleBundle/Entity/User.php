<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="primaryEmail", type="string", length=255)
     */
    private $primaryEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="passowrd", type="string", length=255)
     */
    private $passowrd;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255)
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="userBio", type="string", length=255)
     */
    private $userBio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDate", type="date")
     */
    private $birthDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="verified", type="boolean")
     */
    private $verified;
    
    /**
     * @var Notification[]
     * 
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user")
     */
    private $notifications;
    
    /**
     *
     * @var Claim[]
     * @ORM\OneToMany(targetEntity="Claim", mappedBy="user")
     */
    private $claims;
    
    
    /**
     * @var Message[]
     * 
     * @ORM\OneToMany(targetEntity="Message", mappedBy="user")
     */
    private $messages;
    
    /**
     * @var Request[]
     * 
     * @ORM\OneToMany(targetEntity="Request", mappedBy="user")
     */
    private $requests;
    
    

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
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set primaryEmail
     *
     * @param string $primaryEmail
     * @return User
     */
    public function setPrimaryEmail($primaryEmail)
    {
        $this->primaryEmail = $primaryEmail;

        return $this;
    }

    /**
     * Get primaryEmail
     *
     * @return string 
     */
    public function getPrimaryEmail()
    {
        return $this->primaryEmail;
    }

    /**
     * Set passowrd
     *
     * @param string $passowrd
     * @return User
     */
    public function setPassowrd($passowrd)
    {
        $this->passowrd = $passowrd;

        return $this;
    }

    /**
     * Get passowrd
     *
     * @return string 
     */
    public function getPassowrd()
    {
        return $this->passowrd;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set userBio
     *
     * @param string $userBio
     * @return User
     */
    public function setUserBio($userBio)
    {
        $this->userBio = $userBio;

        return $this;
    }

    /**
     * Get userBio
     *
     * @return string 
     */
    public function getUserBio()
    {
        return $this->userBio;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime 
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set verified
     *
     * @param boolean $verified
     * @return User
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return boolean 
     */
    public function getVerified()
    {
        return $this->verified;
    }
}

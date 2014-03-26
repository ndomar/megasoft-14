<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEmails
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserEmail
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
     * @return UserEmails
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
     * Set email
     *
     * @param string $email
     * @return UserEmails
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set user
     *
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @return UserEmail
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

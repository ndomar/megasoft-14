<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tangle
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tangle
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
     * @ORM\Column(name="icon", type="string", length=255)
     */
    private $icon;
    
    /**
     *
     * @var Request[]
     * @ORM\OneToMany(targetEntity="Request", mappedBy="tangle")
     */
    private $requests;
    
    /**
     *
     * @var Claim[]
     * @ORM\OneToMany(targetEntity="Claim", mappedBy="tangle")
     */
    private $claims;
    
    /**
     * @var UserTangle[]
     * 
     * @ORM\OneToMany(targetEntity="UserTangle", mappedBy="tangle")
     */
    private $userTangles;

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
     * @return Tangle
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
     * Set icon
     *
     * @param string $icon
     * @return Tangle
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
     * Constructor
     */
    public function __construct()
    {
        $this->requests = new \Doctrine\Common\Collections\ArrayCollection();
        $this->claims = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userTangles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add requests
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $requests
     * @return Tangle
     */
    public function addRequest(\Megasoft\EntangleBundle\Entity\Request $requests)
    {
        $this->requests[] = $requests;

        return $this;
    }

    /**
     * Remove requests
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $requests
     */
    public function removeRequest(\Megasoft\EntangleBundle\Entity\Request $requests)
    {
        $this->requests->removeElement($requests);
    }

    /**
     * Get requests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Add claims
     *
     * @param \Megasoft\EntangleBundle\Entity\Claim $claims
     * @return Tangle
     */
    public function addClaim(\Megasoft\EntangleBundle\Entity\Claim $claims)
    {
        $this->claims[] = $claims;

        return $this;
    }

    /**
     * Remove claims
     *
     * @param \Megasoft\EntangleBundle\Entity\Claim $claims
     */
    public function removeClaim(\Megasoft\EntangleBundle\Entity\Claim $claims)
    {
        $this->claims->removeElement($claims);
    }

    /**
     * Get claims
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /**
     * Add userTangles
     *
     * @param \Megasoft\EntangleBundle\Entity\UserTangle $userTangles
     * @return Tangle
     */
    public function addUserTangle(\Megasoft\EntangleBundle\Entity\UserTangle $userTangles)
    {
        $this->userTangles[] = $userTangles;

        return $this;
    }

    /**
     * Remove userTangles
     *
     * @param \Megasoft\EntangleBundle\Entity\UserTangle $userTangles
     */
    public function removeUserTangle(\Megasoft\EntangleBundle\Entity\UserTangle $userTangles)
    {
        $this->userTangles->removeElement($userTangles);
    }

    /**
     * Get userTangles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserTangles()
    {
        return $this->userTangles;
    }
    
    /**
     * Get Users
     *
     * @return User[]
     */
    public function getUsers()
    {
        $users = array();
        foreach($this->userTangles as $userTangle){
            $users[] = $userTangle->getUser();
        }
        return $users;
    }
}

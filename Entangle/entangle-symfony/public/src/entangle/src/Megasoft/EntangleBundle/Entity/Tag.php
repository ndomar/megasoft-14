<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tag
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
     * @var Request[]
     * 
     * @ORM\ManyToMany(targetEntity="Request", mappedBy="tags", cascade={"persist"})
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
     * @return Tag
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
     * Constructor
     */
    public function __construct()
    {
        $this->requests = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add requests
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $requests
     * @return Tag
     */
    public function addRequest(\Megasoft\EntangleBundle\Entity\Request $requests)
    {
        //$this->requests[] = $requests;
        throw new Exception('Not implemented');
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
     * Converts Tag to string
     * 
     * @return string
     */
    public function __toString(){
        return (string) $this->getId();
    }
}

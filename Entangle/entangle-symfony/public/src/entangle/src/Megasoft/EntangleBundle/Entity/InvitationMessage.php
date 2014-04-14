<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InvitationMessage
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class InvitationMessage
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
     * @ORM\Column(name="body", type="string", length=255)
     */
    private $body;
    
    /**
     * @var PendingInvitation[]
     * 
     * @ORM\OneToMany(targetEntity="PendingInvitation", mappedBy="message", cascade={"persist"})
     */
    private $pendingInvitations;


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
     * Set body
     *
     * @param string $body
     * @return InvitationMessage
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pendingInvitations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add pendingInvitations
     *
     * @param \Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitations
     * @return InvitationMessage
     */
    public function addPendingInvitation(\Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitations)
    {
        $this->pendingInvitations[] = $pendingInvitations;

        return $this;
    }

    /**
     * Remove pendingInvitations
     *
     * @param \Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitations
     */
    public function removePendingInvitation(\Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitations)
    {
        $this->pendingInvitations->removeElement($pendingInvitations);
    }

    /**
     * Get pendingInvitations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPendingInvitations()
    {
        return $this->pendingInvitations;
    }
}

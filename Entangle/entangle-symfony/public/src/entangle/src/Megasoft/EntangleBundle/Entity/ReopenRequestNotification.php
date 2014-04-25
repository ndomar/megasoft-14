<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ReopenRequestNotification extends Notification
{
    /**
     * @var integer
     * 
     * @ORM\Column(name="reopenRequestId", type="integer")
     */
    private $requestId;

    /**
     * @var \Megasoft\EntangleBundle\Entity\Request
     * 
     * @ORM\ManyToOne(targetEntity="Request")
     * @ORM\JoinColumn(name="reopenRequestId", referencedColumnName="id")
     */
    private $request;

    /**
     * Set requestId
     *
     * @param integer $requestId
     * @return ReopenRequestNotification
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Get requestId
     *
     * @return integer 
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set request
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $request
     * @return ReopenRequestNotification
     */
    public function setRequest(\Megasoft\EntangleBundle\Entity\Request $request = null)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return \Megasoft\EntangleBundle\Entity\Request 
     */
    public function getRequest()
    {
        return $this->request;
    }
}

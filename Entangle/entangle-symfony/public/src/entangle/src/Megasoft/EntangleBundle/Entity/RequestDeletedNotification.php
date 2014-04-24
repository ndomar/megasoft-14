<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RequestDeletedNotification
 *
 * @ORM\Entity
 */
class RequestDeletedNotification extends Notification
{

    /**
     * @var integer
     *
     * @ORM\Column(name="deletedRequestId", type="integer")
     */
    private $requestId;
    
    /**
     *
     * @var Request
     * 
     * @ORM\ManyToOne(targetEntity="Request", inversedBy="requestDeletedNotifications")
     * @ORM\JoinColumn(name="deletedRequestId", referencedColumnName="id")
     */
    private $request;


    /**
     * Set requestId
     *
     * @param integer $requestId
     * @return RequestDeletedNotification
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
     * @return RequestDeletedNotification
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

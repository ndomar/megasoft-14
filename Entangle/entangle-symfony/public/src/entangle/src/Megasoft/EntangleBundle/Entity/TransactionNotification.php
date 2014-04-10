<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TransactionNotification extends Notification
{
    /**
     * @var integer
     * 
     * @ORM\Column(name="transactionId", type="integer")
     */
    private $transactionId;

    /**
     * @var \Megasoft\EntangleBundle\Entity\Transaction
     * 
     * @ORM\ManyToOne(targetEntity="Transaction", inversedBy="notifications")
     * @ORM\JoinColumn(name="transactionId", referencedColumnName="id")
     */
    private $transaction;


    /**
     * Set transactionId
     *
     * @param integer $transactionId
     * @return TransactionNotification
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return integer 
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set transaction
     *
     * @param \Megasoft\EntangleBundle\Entity\Transaction $transaction
     * @return TransactionNotification
     */
    public function setTransaction(\Megasoft\EntangleBundle\Entity\Transaction $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \Megasoft\EntangleBundle\Entity\Transaction 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
